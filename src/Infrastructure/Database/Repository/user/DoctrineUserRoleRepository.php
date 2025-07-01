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

    /**
     * @throws ORMException
     */
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

            // Establecer las relaciones
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
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.user = :userId')
            ->andWhere('ur.active = true')
            ->setParameter('userId', $userId)
            ->orderBy('ur.id', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findRolesByUserId(int $userId, bool $web): ?array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->join('ur.role', 'r')
            ->where('ur.user = :userId')
            ->andWhere('ur.active = :active')
            ->andWhere('r.web = :web')
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->setParameter('web', $web)
            ->getQuery();

        $result = $query->getResult();

        if (empty($result)) {
            return null;
        }

        return array_map([$this, 'toDomain'], $result);
    }

    public function findActiveRolesByUserId(int $userId): array
    {
        return $this->findUserRolesByUserId($userId);
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

    public function findByUserAndRole(UserModel $user, RoleModel $role): ?UserRoleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.user = :userId')
            ->andWhere('ur.role = :roleId')
            ->andWhere('ur.active = true')
            ->setParameter('userId', $user->getId())
            ->setParameter('roleId', $role->getId())
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function assignRoleToUser(UserModel $user, RoleModel $role): UserRoleModel
    {
        try {
            // Verificar si ya existe esta asignación
            $existing = $this->findByUserAndRole($user, $role);

            if ($existing) {
                // Si existe y está activo, retornarlo
                if ($existing->isActive()) {
                    return $existing;
                }

                // Si existe pero está inactivo, crear uno nuevo (el anterior se mantiene como historial)
            }

            // Crear nueva asignación
            $entity = new UserRoleEntity();
            $entity->setActive(true);

            $userEntity = $this->entityManager->getReference(UserEntity::class, $user->getId());
            $entity->setUser($userEntity);

            $roleEntity = $this->entityManager->getReference(RoleEntity::class, $role->getId());
            $entity->setRole($roleEntity);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->toDomain($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error assigning role to user: ' . $e->getMessage());
        }
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
            ->select('COUNT(ur.id)')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.role = :roleId')
            ->andWhere('ur.active = true')
            ->setParameter('roleId', $roleId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
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

    // Métodos auxiliares para compatibilidad con casos de uso existentes
    public function assignRoleToUserById(int $userId, int $roleId): bool
    {
        try {
            // Verificar si ya existe esta asignación
            $existing = $this->entityManager->createQueryBuilder()
                ->select('ur')
                ->from(UserRoleEntity::class, 'ur')
                ->where('ur.user = :userId')
                ->andWhere('ur.role = :roleId')
                ->setParameter('userId', $userId)
                ->setParameter('roleId', $roleId)
                ->getQuery()
                ->getOneOrNullResult();

            if ($existing) {
                // Si existe pero está inactivo, activarlo
                if (!$existing->isActive()) {
                    $existing->setActive(true);
                    $this->entityManager->flush();
                }
                return true;
            }

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

    public function removeRoleFromUser(int $userId, int $roleId): bool
    {
        try {
            $entity = $this->entityManager->createQueryBuilder()
                ->select('ur')
                ->from(UserRoleEntity::class, 'ur')
                ->where('ur.user = :userId')
                ->andWhere('ur.role = :roleId')
                ->andWhere('ur.active = true')
                ->setParameter('userId', $userId)
                ->setParameter('roleId', $roleId)
                ->getQuery()
                ->getOneOrNullResult();

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

    public function updateUserRoles(int $userId, array $roleIds): bool
    {
        try {
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
                $this->assignRoleToUserById($userId, $roleId);
            }

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
            ->orderBy('ur.id', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }
}
