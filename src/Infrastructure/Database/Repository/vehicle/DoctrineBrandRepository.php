<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;

class DoctrineBrandRepository implements BrandRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(BrandEntity $entity): BrandModel
    {
        return new BrandModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllBrandByName(string $name): ?BrandModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('b')
            ->from(BrandEntity::class, 'b')
            ->where('b.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveBrand(BrandModel $brandModel): BrandModel
    {
        if ($brandModel->getId()) {
            $entity = $this->entityManager->find(BrandEntity::class, $brandModel->getId());
        } else {
            $entity = new BrandEntity();
        }

        $entity->setName($brandModel->getName());
        $entity->setActive($brandModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    // New CRUD methods
    public function findAll(int $page, int $perPage, ?string $search = null, ?string $name = null, ?bool $active = null, string $sortBy = 'name', string $sortDirection = 'asc', bool $onlyActive = false): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('b')
            ->from(BrandEntity::class, 'b');

        // Apply filters
        if ($search) {
            $qb->andWhere('b.name LIKE :search')
              ->setParameter('search', '%' . $search . '%');
        }

        if ($name) {
            $qb->andWhere('b.name LIKE :name')
              ->setParameter('name', '%' . $name . '%');
        }

        if ($active !== null) {
            $qb->andWhere('b.active = :active')
              ->setParameter('active', $active);
        }

        if ($onlyActive) {
            $qb->andWhere('b.active = true');
        }

        // Apply sorting
        $validSortFields = ['id', 'name', 'active'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'name';
        }
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
        $qb->orderBy('b.' . $sortBy, $sortDirection);

        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $qb->setFirstResult($offset)->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?BrandModel
    {
        $entity = $this->entityManager->find(BrandEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(BrandModel $brandModel): BrandModel
    {
        $entity = new BrandEntity();
        $entity->setName($brandModel->getName());
        $entity->setActive($brandModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(BrandModel $brandModel): BrandModel
    {
        $entity = $this->entityManager->find(BrandEntity::class, $brandModel->getId());

        if (!$entity) {
            throw new \InvalidArgumentException('Brand not found');
        }

        $entity->setName($brandModel->getName());
        $entity->setActive($brandModel->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(BrandEntity::class, $id);

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
            ->select('COUNT(b.id)')
            ->from(BrandEntity::class, 'b')
            ->where('b.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('b.id != :excludeId')
              ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function count(?string $search = null, ?string $name = null, ?bool $active = null, bool $onlyActive = false): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(b.id)')
            ->from(BrandEntity::class, 'b');

        // Apply same filters as findAll
        if ($search) {
            $qb->andWhere('b.name LIKE :search')
              ->setParameter('search', '%' . $search . '%');
        }

        if ($name) {
            $qb->andWhere('b.name LIKE :name')
              ->setParameter('name', '%' . $name . '%');
        }

        if ($active !== null) {
            $qb->andWhere('b.active = :active')
              ->setParameter('active', $active);
        }

        if ($onlyActive) {
            $qb->andWhere('b.active = true');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}