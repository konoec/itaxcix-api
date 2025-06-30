<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\TucStatusModel;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucStatusEntity;

class DoctrineTucStatusRepository implements TucStatusRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(TucStatusEntity $entity): TucStatusModel
    {
        return new TucStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllTucStatusByName(string $name): ?TucStatusModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TucStatusEntity::class, 't')
            ->where('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveTucStatus(TucStatusModel $tucStatusModel): TucStatusModel
    {
        if ($tucStatusModel->getId()) {
            $entity = $this->entityManager->find(TucStatusEntity::class, $tucStatusModel->getId());
        } else {
            $entity = new TucStatusEntity();
        }

        $entity->setName($tucStatusModel->getName());
        $entity->setActive($tucStatusModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TucStatusEntity::class, 't');
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

    public function findById(int $id): ?TucStatusModel
    {
        $entity = $this->entityManager->find(TucStatusEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(TucStatusEntity::class, $id);
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
            ->from(TucStatusEntity::class, 't')
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
            ->from(TucStatusEntity::class, 't');
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