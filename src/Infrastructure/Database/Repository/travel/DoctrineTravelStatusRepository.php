<?php

namespace itaxcix\Infrastructure\Database\Repository\travel;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\travel\TravelStatusModel;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\travel\TravelStatusEntity;

class DoctrineTravelStatusRepository implements TravelStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    public function toDomain(TravelStatusEntity $entity): TravelStatusModel {
        return new TravelStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findTravelStatusByName(string $name): ?TravelStatusModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ts')
            ->from(TravelStatusEntity::class, 'ts')
            ->where('ts.name = :name')
            ->andWhere('ts.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveTravelStatus(TravelStatusModel $travelStatusModel): TravelStatusModel
    {
        if ($travelStatusModel->getId()) {
            $entity = $this->entityManager->find(TravelStatusEntity::class, $travelStatusModel->getId());
        } else {
            $entity = new TravelStatusEntity();
        }
        $entity->setName($travelStatusModel->getName());
        $entity->setActive($travelStatusModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TravelStatusEntity::class, 't');
        if (!empty($filters['search'])) {
            $qb->andWhere('t.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('t.active = :active')
                ->setParameter('active', $filters['active']);
        }
        $qb->orderBy('t.' . $sortBy, $sortDirection)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);
        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?TravelStatusModel
    {
        $entity = $this->entityManager->find(TravelStatusEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(TravelStatusEntity::class, $id);
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
            ->select('COUNT(t.id)')
            ->from(TravelStatusEntity::class, 't')
            ->where('t.name = :name')
            ->setParameter('name', $name);
        if ($excludeId !== null) {
            $qb->andWhere('t.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }
        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(TravelStatusEntity::class, 't');
        if (!empty($filters['search'])) {
            $qb->andWhere('t.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('t.active = :active')
                ->setParameter('active', $filters['active']);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}