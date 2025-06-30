<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\vehicle\ColorModel;
use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ColorEntity;

class DoctrineColorRepository implements ColorRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ColorEntity $entity): ColorModel
    {
        return new ColorModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllColorByName(string $name): ?ColorModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(ColorEntity::class, 'c')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveColor(ColorModel $colorModel): ColorModel
    {
        if ($colorModel->getId()) {
            $entity = $this->entityManager->find(ColorEntity::class, $colorModel->getId());
        } else {
            $entity = new ColorEntity();
        }

        $entity->setName($colorModel->getName());
        $entity->setActive($colorModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(): array
    {
        $entities = $this->entityManager->getRepository(ColorEntity::class)->findAll();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?ColorModel
    {
        $entity = $this->entityManager->find(ColorEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(ColorModel $colorModel): ColorModel
    {
        $entity = new ColorEntity();
        $entity->setName($colorModel->getName());
        $entity->setActive($colorModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(ColorModel $colorModel): ColorModel
    {
        $entity = $this->entityManager->find(ColorEntity::class, $colorModel->getId());
        if (!$entity) {
            throw new \InvalidArgumentException("Color with ID {$colorModel->getId()} not found");
        }

        $entity->setName($colorModel->getName());
        $entity->setActive($colorModel->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(ColorEntity::class, $id);
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
            ->select('COUNT(c.id)')
            ->from(ColorEntity::class, 'c')
            ->where('c.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('c.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findWithPagination(int $page, int $perPage, array $filters = [], string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->createFilteredQueryBuilder($filters);

        $qb->orderBy("c.{$sortBy}", $sortDirection)
           ->setFirstResult(($page - 1) * $perPage)
           ->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function getTotalCount(array $filters = []): int
    {
        $qb = $this->createFilteredQueryBuilder($filters);
        $qb->select('COUNT(c.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function createFilteredQueryBuilder(array $filters = []): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(ColorEntity::class, 'c');

        if (!empty($filters['search'])) {
            $qb->andWhere('c.name LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('c.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $qb->andWhere('c.active = :active')
               ->setParameter('active', (bool) $filters['active']);
        }

        return $qb;
    }
}