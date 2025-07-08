<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use InvalidArgumentException;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;
use RuntimeException;

class DoctrineRoleRepository implements RoleRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(object $entity): RoleModel {
        if (!$entity instanceof RoleEntity) {
            throw new InvalidArgumentException('Entity must be instance of RoleEntity');
        }

        return new RoleModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive(),
            web: $entity->isWeb()
        );
    }

    public function findRoleByName(string $name): ?RoleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.name = :name')
            ->andWhere('r.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $results = $query->getResult();
        return !empty($results) ? $this->toDomain($results[0]) : null;
    }

    public function findRoleById(int $id): ?RoleModel
    {
        $entity = $this->entityManager->find(RoleEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAllRoles(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.active = true')
            ->orderBy('r.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findWebRoles(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.active = true')
            ->andWhere('r.web = true')
            ->orderBy('r.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findMobileRoles(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.active = true')
            ->andWhere('r.web = false')
            ->orderBy('r.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveRole(RoleModel $role): RoleModel
    {
        try {
            if ($role->getId()) {
                $entity = $this->entityManager->find(RoleEntity::class, $role->getId());
                if (!$entity) {
                    throw new \RuntimeException('Role not found for update');
                }
            } else {
                $entity = new RoleEntity();
            }

            $entity->setName($role->getName());
            $entity->setActive($role->isActive());
            $entity->setWeb($role->isWeb());

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->toDomain($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error saving role: ' . $e->getMessage());
        }
    }

    public function deleteRole(RoleModel $role): void
    {
        try {
            $entity = $this->entityManager->find(RoleEntity::class, $role->getId());
            if ($entity) {
                $entity->setActive(false);
                $this->entityManager->flush();
            }
        } catch (Exception $e) {
            throw new RuntimeException('Error deleting role: ' . $e->getMessage());
        }
    }

    public function deleteRoleById(int $roleId): bool
    {
        try {
            $entity = $this->entityManager->find(RoleEntity::class, $roleId);
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

    public function findAllPaginated(
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?bool $webOnly = null,
        ?bool $activeOnly = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r');

        // Aplicar filtros
        if ($activeOnly !== null) {
            $qb->andWhere('r.active = :active')
               ->setParameter('active', $activeOnly);
        }

        if ($webOnly !== null) {
            $qb->andWhere('r.web = :web')
               ->setParameter('web', $webOnly);
        }

        if ($search !== null && !empty(trim($search))) {
            $qb->andWhere('r.name LIKE :search')
               ->setParameter('search', '%' . trim($search) . '%');
        }

        // Aplicar paginación
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit)
           ->orderBy('r.name', 'ASC');

        $results = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $results);
    }

    public function countAll(
        ?string $search = null,
        ?bool $webOnly = null,
        ?bool $activeOnly = null
    ): int {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(RoleEntity::class, 'r');

        // Aplicar los mismos filtros que en findAllPaginated
        if ($activeOnly !== null) {
            $qb->andWhere('r.active = :active')
               ->setParameter('active', $activeOnly);
        }

        if ($webOnly !== null) {
            $qb->andWhere('r.web = :web')
               ->setParameter('web', $webOnly);
        }

        if ($search !== null && !empty(trim($search))) {
            $qb->andWhere('r.name LIKE :search')
               ->setParameter('search', '%' . trim($search) . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function roleExists(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(RoleEntity::class, 'r')
            ->where('r.name = :name')
            ->andWhere('r.active = true')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('r.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function updateRoleStatus(int $roleId, bool $active): bool
    {
        try {
            $entity = $this->entityManager->find(RoleEntity::class, $roleId);
            if ($entity) {
                $entity->setActive($active);
                $this->entityManager->flush();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findRolesByIds(array $roleIds): array
    {
        if (empty($roleIds)) {
            return [];
        }

        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.id IN (:roleIds)')
            ->andWhere('r.active = true')
            ->setParameter('roleIds', $roleIds)
            ->orderBy('r.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    // Métodos requeridos por la interfaz
    public function findById(int $id): ?RoleModel
    {
        return $this->findRoleById($id);
    }

    public function save(RoleModel $role): RoleModel
    {
        return $this->saveRole($role);
    }
}
