<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;
use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleCategoryEntity;

class DoctrineVehicleCategoryRepository implements VehicleCategoryRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(VehicleCategoryEntity $entity): VehicleCategoryModel
    {
        return new VehicleCategoryModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findAllVehicleCategoryByName(string $name): ?VehicleCategoryModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vc')
            ->from(VehicleCategoryEntity::class, 'vc')
            ->where('vc.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveVehicleCategory(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel
    {
        if ($vehicleCategoryModel->getId()) {
            $entity = $this->entityManager->find(VehicleCategoryEntity::class, $vehicleCategoryModel->getId());
        } else {
            $entity = new VehicleCategoryEntity();
        }

        $entity->setName($vehicleCategoryModel->getName());
        $entity->setActive($vehicleCategoryModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(): array
    {
        $entities = $this->entityManager->getRepository(VehicleCategoryEntity::class)->findAll();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?VehicleCategoryModel
    {
        $entity = $this->entityManager->find(VehicleCategoryEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel
    {
        $entity = new VehicleCategoryEntity();
        $entity->setName($vehicleCategoryModel->getName());
        $entity->setActive($vehicleCategoryModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel
    {
        $entity = $this->entityManager->find(VehicleCategoryEntity::class, $vehicleCategoryModel->getId());
        if (!$entity) {
            throw new \InvalidArgumentException("VehicleCategory with ID {$vehicleCategoryModel->getId()} not found");
        }

        $entity->setName($vehicleCategoryModel->getName());
        $entity->setActive($vehicleCategoryModel->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(VehicleCategoryEntity::class, $id);
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
            ->select('COUNT(vc.id)')
            ->from(VehicleCategoryEntity::class, 'vc')
            ->where('vc.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('vc.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findWithPagination(int $page, int $perPage, array $filters = [], string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->createFilteredQueryBuilder($filters);

        $qb->orderBy("vc.{$sortBy}", $sortDirection)
           ->setFirstResult(($page - 1) * $perPage)
           ->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function getTotalCount(array $filters = []): int
    {
        $qb = $this->createFilteredQueryBuilder($filters);
        $qb->select('COUNT(vc.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function createFilteredQueryBuilder(array $filters = []): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('vc')
            ->from(VehicleCategoryEntity::class, 'vc');

        if (!empty($filters['search'])) {
            $qb->andWhere('vc.name LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('vc.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $qb->andWhere('vc.active = :active')
               ->setParameter('active', (bool) $filters['active']);
        }

        return $qb;
    }
}