<?php

namespace itaxcix\Infrastructure\Database\Repository\incident;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;

class DoctrineIncidentTypeRepository implements IncidentTypeRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(IncidentTypeEntity $entity): IncidentTypeModel {
        return new IncidentTypeModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findIncidentTypeByName(string $name): ?IncidentTypeModel {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(IncidentTypeEntity::class, 't')
            ->where('t.name = :name')
            ->andWhere('t.active = true')
            ->setParameter('name', $name)
            ->getQuery();
        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveIncidentType(IncidentTypeModel $incidentTypeModel): IncidentTypeModel
    {
        if ($incidentTypeModel->getId()) {
            $entity = $this->entityManager->find(IncidentTypeEntity::class, $incidentTypeModel->getId());
        } else {
            $entity = new IncidentTypeEntity();
        }
        $entity->setName($incidentTypeModel->getName());
        $entity->setActive($incidentTypeModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from(IncidentTypeEntity::class, 'i');
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

    public function findById(int $id): ?IncidentTypeModel
    {
        $entity = $this->entityManager->find(IncidentTypeEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(IncidentTypeEntity::class, $id);
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
            ->from(IncidentTypeEntity::class, 'i')
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
            ->from(IncidentTypeEntity::class, 'i');
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
