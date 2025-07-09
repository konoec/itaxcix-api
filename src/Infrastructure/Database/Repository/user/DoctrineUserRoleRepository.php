<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity;

class DoctrineUserRoleRepository implements UserRoleRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private RoleRepositoryInterface $roleRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(EntityManagerInterface $entityManager, RoleRepositoryInterface $roleRepository, UserRepositoryInterface $userRepository) {
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    public function findActiveRolesByUserId(int $userId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->join('ur.role', 'r')
            ->where('ur.user = :userId')
            ->andWhere('ur.active = true')
            ->andWhere('r.active = true')
            ->setParameter('userId', $userId)
            ->orderBy('ur.id', 'DESC')
            ->getQuery();

        $entities = $query->getResult();
        // Filtrar duplicados por rol (solo el más reciente por cada rol)
        $uniqueRoles = [];
        foreach ($entities as $entity) {
            $roleId = $entity->getRole()->getId();
            if (!isset($uniqueRoles[$roleId])) {
                $uniqueRoles[$roleId] = $entity;
            }
        }
        return array_map([$this, 'toDomain'], array_values($uniqueRoles));
    }

    public function findByUserAndRole(UserModel $user, RoleModel $role): ?UserRoleModel
    {
        // Buscar solo el más reciente para evitar el error de múltiples resultados
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.user = :userId')
            ->andWhere('ur.role = :roleId')
            ->andWhere('ur.active = true')
            ->setParameter('userId', $user->getId())
            ->setParameter('roleId', $role->getId())
            ->orderBy('ur.id', 'DESC') // Obtener el más reciente
            ->setMaxResults(1) // Limitar a 1 resultado
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function assignRoleToUser(UserModel $user, RoleModel $role): UserRoleModel
    {
        try {
            // Buscar si ya existe un registro para este usuario-rol
            $existingEntity = $this->entityManager->createQueryBuilder()
                ->select('ur')
                ->from(UserRoleEntity::class, 'ur')
                ->where('ur.user = :userId')
                ->andWhere('ur.role = :roleId')
                ->setParameter('userId', $user->getId())
                ->setParameter('roleId', $role->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingEntity) {
                // Si existe, simplemente lo activamos
                $existingEntity->setActive(true);
                $entity = $existingEntity;
            } else {
                // Si no existe, creamos uno nuevo
                $entity = new UserRoleEntity();
                $entity->setActive(true);

                $userEntity = $this->entityManager->getReference(UserEntity::class, $user->getId());
                $entity->setUser($userEntity);

                $roleEntity = $this->entityManager->getReference(RoleEntity::class, $role->getId());
                $entity->setRole($roleEntity);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->toDomain($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error assigning role to user: ' . $e->getMessage());
        }
    }

    public function updateUserRoles(int $userId, array $roleIds): bool
    {
        try {
            $this->entityManager->beginTransaction();

            // Desactivar todos los roles actuales del usuario
            $this->entityManager->createQueryBuilder()
                ->update(UserRoleEntity::class, 'ur')
                ->set('ur.active', 'false')
                ->where('ur.user = :userId')
                ->setParameter('userId', $userId)
                ->getQuery()
                ->execute();

            // Asignar los nuevos roles
            foreach ($roleIds as $roleId) {
                // Crear nueva asignación para cada rol
                $entity = new UserRoleEntity();
                $entity->setActive(true);

                $userEntity = $this->entityManager->getReference(UserEntity::class, $userId);
                $entity->setUser($userEntity);

                $roleEntity = $this->entityManager->getReference(RoleEntity::class, $roleId);
                $entity->setRole($roleEntity);

                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            return true;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new \RuntimeException('Error updating user roles: ' . $e->getMessage());
        }
    }

    public function cleanDuplicateUserRoles(int $userId): bool
    {
        try {
            // Obtener todos los roles duplicados para el usuario
            $duplicates = $this->entityManager->createQueryBuilder()
                ->select('ur.id, ur.role as roleId, COUNT(ur.id) as cnt')
                ->from(UserRoleEntity::class, 'ur')
                ->where('ur.user = :userId')
                ->andWhere('ur.active = true')
                ->setParameter('userId', $userId)
                ->groupBy('ur.role')
                ->having('COUNT(ur.id) > 1')
                ->getQuery()
                ->getResult();

            foreach ($duplicates as $duplicate) {
                // Mantener solo el más reciente para cada rol
                $this->entityManager->createQueryBuilder()
                    ->update(UserRoleEntity::class, 'ur')
                    ->set('ur.active', 'false')
                    ->where('ur.user = :userId')
                    ->andWhere('ur.role = :roleId')
                    ->andWhere('ur.id NOT IN (
                        SELECT MAX(ur2.id) 
                        FROM ' . UserRoleEntity::class . ' ur2 
                        WHERE ur2.user = :userId 
                        AND ur2.role = :roleId 
                        AND ur2.active = true
                    )')
                    ->setParameter('userId', $userId)
                    ->setParameter('roleId', $duplicate['roleId'])
                    ->getQuery()
                    ->execute();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Métodos existentes sin cambios
    public function saveUserRole(UserRoleModel $userRole): UserRoleModel
    {
        try {
            if ($userRole->getId()) {
                $entity = $this->entityManager->find(UserRoleEntity::class, $userRole->getId());
                if (!$entity) {
                    throw new \RuntimeException('UserRole not found for update');
                }
            } else {
                $entity = new UserRoleEntity();
            }

            $entity->setActive($userRole->isActive());

            $userEntity = $this->entityManager->getReference(UserEntity::class, $userRole->getUser()->getId());
            $entity->setUser($userEntity);

            $roleEntity = $this->entityManager->getReference(RoleEntity::class, $userRole->getRole()->getId());
            $entity->setRole($roleEntity);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->toDomain($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error saving user role: ' . $e->getMessage());
        }
    }

    public function findUserRoleById(int $id): ?UserRoleModel
    {
        $entity = $this->entityManager->find(UserRoleEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserRolesByUserId(int $userId): array
    {
        return $this->findActiveRolesByUserId($userId);
    }

    public function findRolesByUserId(int $userId, bool $web): ?array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->join('ur.role', 'r')
            ->where('ur.user = :userId')
            ->andWhere('ur.active = true')
            ->andWhere('r.active = true')
            ->andWhere('r.web = :web')
            ->setParameter('userId', $userId)
            ->setParameter('web', $web)
            ->groupBy('r.id')
            ->orderBy('ur.id', 'DESC')
            ->getQuery();

        $result = $query->getResult();
        return empty($result) ? null : array_map([$this, 'toDomain'], $result);
    }

    public function findAllUserRoles(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.active = true')
            ->orderBy('ur.id', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function deleteUserRole(UserRoleModel $userRole): bool
    {
        try {
            $entity = $this->entityManager->find(UserRoleEntity::class, $userRole->getId());
            if ($entity) {
                $entity->setActive(false);
                $this->entityManager->flush();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeAllByUserId(int $userId): bool
    {
        try {
            $this->entityManager->createQueryBuilder()
                ->update(UserRoleEntity::class, 'ur')
                ->set('ur.active', 'false')
                ->where('ur.user = :userId')
                ->setParameter('userId', $userId)
                ->getQuery()
                ->execute();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasActiveUsersByRoleId(int $roleId): bool
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT ur.user)')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.role = :roleId')
            ->andWhere('ur.active = true')
            ->setParameter('roleId', $roleId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function removeRoleFromUser(int $userId, int $roleId): bool
    {
        try {
            $this->entityManager->createQueryBuilder()
                ->update(UserRoleEntity::class, 'ur')
                ->set('ur.active', 'false')
                ->where('ur.user = :userId')
                ->andWhere('ur.role = :roleId')
                ->setParameter('userId', $userId)
                ->setParameter('roleId', $roleId)
                ->getQuery()
                ->execute();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function assignRoleToUserById(int $userId, int $roleId): bool
    {
        try {
            // Desactivar asignaciones previas del mismo rol
            $this->entityManager->createQueryBuilder()
                ->update(UserRoleEntity::class, 'ur')
                ->set('ur.active', 'false')
                ->where('ur.user = :userId')
                ->andWhere('ur.role = :roleId')
                ->setParameter('userId', $userId)
                ->setParameter('roleId', $roleId)
                ->getQuery()
                ->execute();

            // Crear nueva asignación
            $entity = new UserRoleEntity();
            $entity->setActive(true);

            $userEntity = $this->entityManager->getReference(UserEntity::class, $userId);
            $entity->setUser($userEntity);

            $roleEntity = $this->entityManager->getReference(RoleEntity::class, $roleId);
            $entity->setRole($roleEntity);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findAllUserRoleByUserId(int $userId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('ur.id', 'DESC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function toDomain(object $entity): UserRoleModel
    {
        if (!$entity instanceof UserRoleEntity) {
            throw new \InvalidArgumentException('Entity must be instance of UserRoleEntity');
        }

        return new UserRoleModel(
            id: $entity->getId(),
            role: $this->roleRepository->toDomain($entity->getRole()),
            user: $this->userRepository->toDomain($entity->getUser()),
            active: $entity->isActive()
        );
    }
}