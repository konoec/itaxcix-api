<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\user\DriverStatusModel;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\DriverStatusEntity;

class DoctrineDriverStatusRepository implements DriverStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DriverStatusEntity $entity): DriverStatusModel {
        return new DriverStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function toEntity(DriverStatusModel $model): DriverStatusEntity {
        $entity = new DriverStatusEntity();
        if ($model->getId() !== null) {
            $entity->setId($model->getId());
        }
        $entity->setName($model->getName());
        $entity->setActive($model->isActive());
        return $entity;
    }

    public function findDriverStatusByName(string $name): ?DriverStatusModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('ds')
            ->from(DriverStatusEntity::class, 'ds')
            ->where('ds.name = :name')
            ->andWhere('ds.active = true')
            ->setParameter('name', $name)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $this->toDomain($result);
    }

    public function findAll(): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('ds')
            ->from(DriverStatusEntity::class, 'ds')
            ->orderBy('ds.name', 'ASC');

        $results = $qb->getQuery()->getResult();

        return array_map(fn($entity) => $this->toDomain($entity), $results);
    }

    public function findById(int $id): ?DriverStatusModel
    {
        $entity = $this->entityManager->find(DriverStatusEntity::class, $id);

        if (!$entity) {
            return null;
        }

        return $this->toDomain($entity);
    }

    public function create(DriverStatusModel $driverStatus): DriverStatusModel
    {
        $entity = $this->toEntity($driverStatus);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(DriverStatusModel $driverStatus): DriverStatusModel
    {
        $entity = $this->entityManager->find(DriverStatusEntity::class, $driverStatus->getId());

        if (!$entity) {
            throw new \InvalidArgumentException("DriverStatus not found");
        }

        $entity->setName($driverStatus->getName());
        $entity->setActive($driverStatus->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(DriverStatusEntity::class, $id);

        if (!$entity) {
            return false;
        }

        // Soft delete - solo marcar como inactivo
        $entity->setActive(false);
        $this->entityManager->flush();

        return true;
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(ds.id)')
            ->from(DriverStatusEntity::class, 'ds')
            ->where('ds.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('ds.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int)$qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findWithFilters(
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        int $page = 1,
        int $perPage = 15,
        bool $onlyActive = false
    ): array {
        $qb = $this->createFilteredQueryBuilder($search, $name, $active, $onlyActive);

        // Aplicar ordenamiento
        $sortDirection = strtoupper($sortDirection);
        $qb->orderBy("ds.{$sortBy}", $sortDirection);

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $qb->setFirstResult($offset)
           ->setMaxResults($perPage);

        $results = $qb->getQuery()->getResult();

        return array_map(fn($entity) => $this->toDomain($entity), $results);
    }

    public function countWithFilters(
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        bool $onlyActive = false
    ): int {
        $qb = $this->createFilteredQueryBuilder($search, $name, $active, $onlyActive);
        $qb->select('COUNT(ds.id)');

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    private function createFilteredQueryBuilder(
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        bool $onlyActive = false
    ): QueryBuilder {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('ds')
            ->from(DriverStatusEntity::class, 'ds');

        // Filtro de búsqueda global
        if ($search !== null && $search !== '') {
            $qb->andWhere('ds.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Filtro por nombre específico
        if ($name !== null && $name !== '') {
            $qb->andWhere('ds.name LIKE :name')
               ->setParameter('name', '%' . $name . '%');
        }

        // Filtro por estado activo
        if ($active !== null) {
            $qb->andWhere('ds.active = :active')
               ->setParameter('active', $active);
        }

        // Filtro solo activos
        if ($onlyActive) {
            $qb->andWhere('ds.active = true');
        }

        return $qb;
    }
}