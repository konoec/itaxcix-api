<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\VehicleClassModel;
use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleClassEntity;

class DoctrineVehicleClassRepository implements VehicleClassRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(VehicleClassEntity $entity): VehicleClassModel
    {
        return new VehicleClassModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllVehicleClassByName(string $name): ?VehicleClassModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleClassEntity::class, 'v')
            ->where('v.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveVehicleClass(VehicleClassModel $vehicleClassModel): VehicleClassModel
    {
        if ($vehicleClassModel->getId()){
            $entity = $this->entityManager->find(VehicleClassEntity::class, $vehicleClassModel->getId());
        } else {
            $entity = new VehicleClassEntity();
        }

        $entity->setName($vehicleClassModel->getName());
        $entity->setActive($vehicleClassModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleClassEntity::class, 'v');

        // Apply filters
        if (!empty($filters['search'])) {
            $qb->andWhere('v.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('v.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['active'])) {
            $qb->andWhere('v.active = :active')
                ->setParameter('active', $filters['active']);
        }

        // Apply sorting
        $qb->orderBy('v.' . $sortBy, $sortDirection);

        // Apply pagination
        $qb->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?VehicleClassModel
    {
        $entity = $this->entityManager->find(VehicleClassEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(VehicleClassEntity::class, $id);
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
            ->select('COUNT(v.id)')
            ->from(VehicleClassEntity::class, 'v')
            ->where('v.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('v.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(v.id)')
            ->from(VehicleClassEntity::class, 'v');

        // Apply filters
        if (!empty($filters['search'])) {
            $qb->andWhere('v.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('v.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['active'])) {
            $qb->andWhere('v.active = :active')
                ->setParameter('active', $filters['active']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}