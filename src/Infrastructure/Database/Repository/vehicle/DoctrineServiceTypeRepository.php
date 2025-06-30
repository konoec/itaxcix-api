<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\ServiceTypeModel;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ServiceTypeEntity;

class DoctrineServiceTypeRepository implements ServiceTypeRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ServiceTypeEntity $entity): ServiceTypeModel
    {
        return new ServiceTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllServiceTypeByName(string $name): ?ServiceTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(ServiceTypeEntity::class, 's')
            ->where('s.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveServiceType(ServiceTypeModel $serviceTypeModel): ServiceTypeModel
    {
        if ($serviceTypeModel->getId()) {
            $entity = $this->entityManager->find(ServiceTypeEntity::class, $serviceTypeModel->getId());
        } else {
            $entity = new ServiceTypeEntity();
        }

        $entity->setName($serviceTypeModel->getName());
        $entity->setActive($serviceTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(ServiceTypeEntity::class, 's');

        // Filtros
        if (!empty($filters['search'])) {
            $qb->andWhere('s.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('s.active = :active')
                ->setParameter('active', $filters['active']);
        }

        // Orden y paginación
        $qb->orderBy('s.' . $sortBy, $sortDirection)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?ServiceTypeModel
    {
        $entity = $this->entityManager->find(ServiceTypeEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(ServiceTypeEntity::class, $id);
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
            ->select('COUNT(s.id)')
            ->from(ServiceTypeEntity::class, 's')
            ->where('s.name = :name')
            ->setParameter('name', $name);
        if ($excludeId !== null) {
            $qb->andWhere('s.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }
        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(ServiceTypeEntity::class, 's');
        if (!empty($filters['search'])) {
            $qb->andWhere('s.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['name'])) {
            $qb->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['active'])) {
            $qb->andWhere('s.active = :active')
                ->setParameter('active', $filters['active']);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}