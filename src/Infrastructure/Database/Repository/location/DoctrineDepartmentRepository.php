<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\location\DepartmentModel;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DepartmentEntity;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;

class DoctrineDepartmentRepository implements DepartmentRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DepartmentEntity $entity): DepartmentModel
    {
        return new DepartmentModel(
            id: $entity->getId(),
            name: $entity->getName(),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findDepartmentByName(string $name): ?DepartmentModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(DepartmentEntity::class, 'd')
            ->where('d.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveDepartment(DepartmentModel $departmentModel): DepartmentModel
    {
        if ($departmentModel->getId()) {
            $entity = $this->entityManager->find(DepartmentEntity::class, $departmentModel->getId());
        } else {
            $entity = new DepartmentEntity();
        }

        $entity->setName($departmentModel->getName());
        $entity->setUbigeo($departmentModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(int $page = 1, int $limit = 15, ?string $search = null, ?string $orderBy = 'name', string $orderDirection = 'ASC'): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(DepartmentEntity::class, 'd');

        if ($search) {
            $queryBuilder
                ->where('d.name LIKE :search OR d.ubigeo LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $queryBuilder->orderBy('d.' . $orderBy, $orderDirection);

        $offset = ($page - 1) * $limit;
        $queryBuilder->setFirstResult($offset)->setMaxResults($limit);

        $entities = $queryBuilder->getQuery()->getResult();

        return array_map(fn($entity) => $this->toDomain($entity), $entities);
    }

    public function findById(int $id): ?DepartmentModel
    {
        $entity = $this->entityManager->find(DepartmentEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(DepartmentModel $departmentModel): DepartmentModel
    {
        $entity = new DepartmentEntity();
        $entity->setName($departmentModel->getName());
        $entity->setUbigeo($departmentModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(DepartmentModel $departmentModel): DepartmentModel
    {
        $entity = $this->entityManager->find(DepartmentEntity::class, $departmentModel->getId());
        if (!$entity) {
            throw new \InvalidArgumentException('Department not found');
        }

        $entity->setName($departmentModel->getName());
        $entity->setUbigeo($departmentModel->getUbigeo());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(DepartmentEntity::class, $id);
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
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DepartmentEntity::class, 'd')
            ->where('d.name = :name')
            ->setParameter('name', $name);

        if ($excludeId) {
            $queryBuilder
                ->andWhere('d.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }

    public function existsByUbigeo(string $ubigeo, ?int $excludeId = null): bool
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DepartmentEntity::class, 'd')
            ->where('d.ubigeo = :ubigeo')
            ->setParameter('ubigeo', $ubigeo);

        if ($excludeId) {
            $queryBuilder
                ->andWhere('d.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }

    public function count(?string $search = null): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DepartmentEntity::class, 'd');

        if ($search) {
            $queryBuilder
                ->where('d.name LIKE :search OR d.ubigeo LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}