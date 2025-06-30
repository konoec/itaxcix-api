<?php

namespace itaxcix\Infrastructure\Database\Repository\infraction;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\infraction\InfractionSeverityModel;
use itaxcix\Core\Interfaces\infraction\InfractionSeverityRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\infraction\InfractionSeverityEntity;

class DoctrineInfractionSeverityRepository implements InfractionSeverityRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(InfractionSeverityEntity $entity): InfractionSeverityModel {
        return new InfractionSeverityModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function saveInfractionSeverity(InfractionSeverityModel $infractionSeverityModel): InfractionSeverityModel
    {
        if ($infractionSeverityModel->getId()) {
            $entity = $this->entityManager->find(InfractionSeverityEntity::class, $infractionSeverityModel->getId());
        } else {
            $entity = new InfractionSeverityEntity();
        }
        $entity->setName($infractionSeverityModel->getName());
        $entity->setActive($infractionSeverityModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from(InfractionSeverityEntity::class, 'i');
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

    public function findById(int $id): ?InfractionSeverityModel
    {
        $entity = $this->entityManager->find(InfractionSeverityEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(InfractionSeverityEntity::class, $id);
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
            ->from(InfractionSeverityEntity::class, 'i')
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
            ->from(InfractionSeverityEntity::class, 'i');
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

