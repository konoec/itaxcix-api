<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use itaxcix\Core\Domain\user\PermissionModel;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\PermissionEntity;
use RuntimeException;

class DoctrinePermissionRepository implements PermissionRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }


    public function findPermissionById(int $id): ?PermissionModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result ? $this->toDomain($result) : null;
    }

    public function findPermissionByName(string $name): ?PermissionModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result ? $this->toDomain($result) : null;
    }

    public function findAllPermissions(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.active = true')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findWebPermissions(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.active = true')
            ->andWhere('p.web = true')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findMobilePermissions(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.active = true')
            ->andWhere('p.web = false')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function savePermission(PermissionModel $permission): PermissionModel
    {
        try {
            if ($permission->getId()) {
                $entity = $this->entityManager->find(PermissionEntity::class, $permission->getId());
                if (!$entity) {
                    throw new RuntimeException('Permission not found for update');
                }
            } else {
                $entity = new PermissionEntity();
            }

            $entity->setName($permission->getName());
            $entity->setActive($permission->isActive());
            $entity->setWeb($permission->isWeb());

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->toDomain($entity);
        } catch (Exception $e) {
            throw new RuntimeException('Error saving permission: ' . $e->getMessage());
        }
    }

    public function deletePermission(PermissionModel $permission): void
    {
        try {
            $entity = $this->entityManager->find(PermissionEntity::class, $permission->getId());
            if ($entity) {
                $entity->setActive(false);
                $this->entityManager->flush();
            }
        } catch (Exception $e) {
            throw new RuntimeException('Error eliminando Permiso: ' . $e->getMessage());
        }
    }

    public function findAllPaginated(
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?bool $webOnly = null,
        ?bool $activeOnly = true
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p');

        // Aplicar filtros
        if ($activeOnly !== null) {
            $qb->andWhere('p.active = :active')
               ->setParameter('active', $activeOnly);
        }

        if ($webOnly !== null) {
            $qb->andWhere('p.web = :web')
               ->setParameter('web', $webOnly);
        }

        if ($search !== null && !empty(trim($search))) {
            $qb->andWhere('p.name LIKE :search')
               ->setParameter('search', '%' . trim($search) . '%');
        }

        // Aplicar paginación
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit)
           ->orderBy('p.name', 'ASC');

        $results = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $results);
    }

    public function countAll(
        ?string $search = null,
        ?bool $webOnly = null,
        ?bool $activeOnly = true
    ): int {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(PermissionEntity::class, 'p');

        // Aplicar los mismos filtros que en findAllPaginated
        if ($activeOnly !== null) {
            $qb->andWhere('p.active = :active')
               ->setParameter('active', $activeOnly);
        }

        if ($webOnly !== null) {
            $qb->andWhere('p.web = :web')
               ->setParameter('web', $webOnly);
        }

        if ($search !== null && !empty(trim($search))) {
            $qb->andWhere('p.name LIKE :search')
               ->setParameter('search', '%' . trim($search) . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function permissionExists(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(PermissionEntity::class, 'p')
            ->where('p.name = :name')
            ->andWhere('p.active = true')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('p.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function updatePermissionStatus(int $permissionId, bool $active): bool
    {
        try {
            $entity = $this->entityManager->find(PermissionEntity::class, $permissionId);
            if ($entity) {
                $entity->setActive($active);
                $this->entityManager->flush();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function findPermissionsByIds(array $permissionIds): array
    {
        if (empty($permissionIds)) {
            return [];
        }

        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.id IN (:permissionIds)')
            ->andWhere('p.active = true')
            ->setParameter('permissionIds', $permissionIds)
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    // Métodos requeridos por la interfaz
    public function findById(int $id): ?PermissionModel
    {
        return $this->findPermissionById($id);
    }

    public function save(PermissionModel $permission): PermissionModel
    {
        return $this->savePermission($permission);
    }

    public function toDomain(object $entity): PermissionModel
    {
        if (!$entity instanceof PermissionEntity) {
            throw new \InvalidArgumentException('Entity must be instance of PermissionEntity');
        }

        return new PermissionModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive(),
            web: $entity->isWeb()
        );
    }

    // Método auxiliar para compatibilidad con casos de uso existentes
    public function deletePermissionById(int $permissionId): bool
    {
        try {
            $entity = $this->entityManager->find(PermissionEntity::class, $permissionId);
            if ($entity) {
                $entity->setActive(false);
                $this->entityManager->flush();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}