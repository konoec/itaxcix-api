<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserCodeTypeModel;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeTypeEntity;

class DoctrineUserCodeTypeRepository implements UserCodeTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function toDomain(UserCodeTypeEntity $entity): UserCodeTypeModel
    {
        return new UserCodeTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findUserCodeTypeByName(string $name): ?UserCodeTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uct')
            ->from(UserCodeTypeEntity::class, 'uct')
            ->where('uct.name = :name')
            ->andWhere('uct.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserCodeTypeById(int $id): ?UserCodeTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uct')
            ->from(UserCodeTypeEntity::class, 'uct')
            ->where('uct.id = :id')
            ->andWhere('uct.active = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('uct')
            ->from(UserCodeTypeEntity::class, 'uct');
        if (!empty($filters['search'])) {
            $qb->andWhere('uct.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('uct.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('uct.active = :active')
                ->setParameter('active', $filters['active']);
        }
        $qb->orderBy('uct.' . $sortBy, $sortDirection)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);
        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function saveUserCodeType(UserCodeTypeModel $userCodeTypeModel): UserCodeTypeModel
    {
        if ($userCodeTypeModel->getId()) {
            $entity = $this->entityManager->find(UserCodeTypeEntity::class, $userCodeTypeModel->getId());
        } else {
            $entity = new UserCodeTypeEntity();
        }
        $entity->setName($userCodeTypeModel->getName());
        $entity->setActive($userCodeTypeModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(UserCodeTypeEntity::class, $id);
        if (!$entity) {
            return false;
        }
        $entity->setActive(false);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return true;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(uct.id)')
            ->from(UserCodeTypeEntity::class, 'uct');
        if (!empty($filters['search'])) {
            $qb->andWhere('uct.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('uct.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('uct.active = :active')
                ->setParameter('active', $filters['active']);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(uct.id)')
            ->from(UserCodeTypeEntity::class, 'uct')
            ->where('uct.name = :name')
            ->setParameter('name', $name);
        if ($excludeId !== null) {
            $qb->andWhere('uct.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }
        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findById(int $id): ?UserCodeTypeModel
    {
        $entity = $this->entityManager->find(UserCodeTypeEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }
}
