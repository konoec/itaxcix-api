<?php

namespace itaxcix\Infrastructure\Database\Repository\infraction;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\infraction\InfractionStatusModel;
use itaxcix\Core\Interfaces\infraction\InfractionStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\infraction\InfractionStatusEntity;

class DoctrineInfractionStatusRepository implements InfractionStatusRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(InfractionStatusEntity $entity): InfractionStatusModel {
        return new InfractionStatusModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function saveInfractionStatus(InfractionStatusModel $infractionStatusModel): InfractionStatusModel
    {
        if ($infractionStatusModel->getId()) {
            $entity = $this->entityManager->find(InfractionStatusEntity::class, $infractionStatusModel->getId());
        } else {
            $entity = new InfractionStatusEntity();
        }
        $entity->setName($infractionStatusModel->getName());
        $entity->setActive($infractionStatusModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from(InfractionStatusEntity::class, 'i');
        if (!empty($filters['search'])) {
            $qb->andWhere('i.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('i.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('i.active = :active')
                ->setParameter('active', $filters['active']);
        }
        $qb->orderBy('i.' . $sortBy, $sortDirection)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);
        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?InfractionStatusModel
    {
        $entity = $this->entityManager->find(InfractionStatusEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(InfractionStatusEntity::class, $id);
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
            ->select('COUNT(i.id)')
            ->from(InfractionStatusEntity::class, 'i')
            ->where('i.name = :name')
            ->setParameter('name', $name);
        if ($excludeId !== null) {
            $qb->andWhere('i.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }
        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(i.id)')
            ->from(InfractionStatusEntity::class, 'i');
        if (!empty($filters['search'])) {
            $qb->andWhere('i.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('i.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('i.active = :active')
                ->setParameter('active', $filters['active']);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

