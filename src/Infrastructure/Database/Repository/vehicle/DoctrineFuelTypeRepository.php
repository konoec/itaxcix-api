<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\vehicle\FuelTypeModel;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\FuelTypeEntity;

class DoctrineFuelTypeRepository implements FuelTypeRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(FuelTypeEntity $entity): FuelTypeModel
    {
        return new FuelTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllFuelTypeByName(string $name): ?FuelTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from(FuelTypeEntity::class, 'f')
            ->where('f.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveFuelType(FuelTypeModel $fuelTypeModel): FuelTypeModel
    {
        if ($fuelTypeModel->getId()) {
            $entity = $this->entityManager->find(FuelTypeEntity::class, $fuelTypeModel->getId());
        } else {
            $entity = new FuelTypeEntity();
        }

        $entity->setName($fuelTypeModel->getName());
        $entity->setActive($fuelTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(): array
    {
        $entities = $this->entityManager->getRepository(FuelTypeEntity::class)->findAll();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?FuelTypeModel
    {
        $entity = $this->entityManager->find(FuelTypeEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(FuelTypeModel $fuelTypeModel): FuelTypeModel
    {
        $entity = new FuelTypeEntity();
        $entity->setName($fuelTypeModel->getName());
        $entity->setActive($fuelTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(FuelTypeModel $fuelTypeModel): FuelTypeModel
    {
        $entity = $this->entityManager->find(FuelTypeEntity::class, $fuelTypeModel->getId());
        if (!$entity) {
            throw new \InvalidArgumentException("FuelType with ID {$fuelTypeModel->getId()} not found");
        }

        $entity->setName($fuelTypeModel->getName());
        $entity->setActive($fuelTypeModel->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(FuelTypeEntity::class, $id);
        if (!$entity) {
            return false;
        }

        $entity->setActive(false);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return true;
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(f.id)')
            ->from(FuelTypeEntity::class, 'f')
            ->where('f.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('f.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findWithPagination(int $page, int $perPage, array $filters = [], string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->createFilteredQueryBuilder($filters);

        $qb->orderBy("f.{$sortBy}", $sortDirection)
           ->setFirstResult(($page - 1) * $perPage)
           ->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function getTotalCount(array $filters = []): int
    {
        $qb = $this->createFilteredQueryBuilder($filters);
        $qb->select('COUNT(f.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function createFilteredQueryBuilder(array $filters = []): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from(FuelTypeEntity::class, 'f');

        if (!empty($filters['search'])) {
            $qb->andWhere('f.name LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('f.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $qb->andWhere('f.active = :active')
               ->setParameter('active', (bool) $filters['active']);
        }

        return $qb;
    }
}