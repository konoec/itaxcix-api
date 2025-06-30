<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ProcedureTypeEntity;

class DoctrineProcedureTypeRepository implements ProcedureTypeRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ProcedureTypeEntity $entity): ProcedureTypeModel
    {
        return new ProcedureTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllProcedureTypeByName(string $name): ?ProcedureTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(ProcedureTypeEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveProcedureType(ProcedureTypeModel $procedureTypeModel): ProcedureTypeModel
    {
        if ($procedureTypeModel->getId()) {
            $entity = $this->entityManager->find(ProcedureTypeEntity::class, $procedureTypeModel->getId());
        } else {
            $entity = new ProcedureTypeEntity();
        }

        $entity->setName($procedureTypeModel->getName());
        $entity->setActive($procedureTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(ProcedureTypeEntity::class, 'p');
        if (!empty($filters['search'])) {
            $qb->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('p.active = :active')
                ->setParameter('active', $filters['active']);
        }
        $qb->orderBy('p.' . $sortBy, $sortDirection)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);
        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?ProcedureTypeModel
    {
        $entity = $this->entityManager->find(ProcedureTypeEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(ProcedureTypeEntity::class, $id);
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
            ->select('COUNT(p.id)')
            ->from(ProcedureTypeEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name);
        if ($excludeId !== null) {
            $qb->andWhere('p.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }
        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(ProcedureTypeEntity::class, 'p');
        if (!empty($filters['search'])) {
            $qb->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('p.active = :active')
                ->setParameter('active', $filters['active']);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}