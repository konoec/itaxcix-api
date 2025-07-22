<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\vehicle\ModelModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ModelEntity;

class DoctrineModelRepository implements ModelRepositoryInterface {

    private EntityManagerInterface $entityManager;
    private BrandRepositoryInterface $brandRepository;

    public function __construct(EntityManagerInterface $entityManager, BrandRepositoryInterface $brandRepository) {
        $this->entityManager = $entityManager;
        $this->brandRepository = $brandRepository;
    }

    public function toDomain(ModelEntity $entity): ModelModel
    {
        return new ModelModel(
            id: $entity->getId(),
            name: $entity->getName(),
            brand: $this->brandRepository->toDomain($entity->getBrand()),
            active: $entity->isActive()
        );
    }

    public function findAllModelByName(string $name): ?ModelModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(ModelEntity::class, 'm')
            ->where('m.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function saveModel(ModelModel $modelModel): ModelModel
    {
        if ($modelModel->getId()) {
            $entity = $this->entityManager->find(ModelEntity::class, $modelModel->getId());
        } else {
            $entity = new ModelEntity();
        }

        $entity->setName($modelModel->getName());
        $entity->setBrand(
            $this->entityManager->getReference(
                BrandEntity::class, $modelModel->getBrand()->getId()
            )
        );
        $entity->setActive($modelModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    // Métodos CRUD adicionales para el panel administrativo
    public function findAll(int $page = 1, int $perPage = 15, array $filters = [], string $sortBy = 'name', string $sortOrder = 'ASC'): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m', 'b')
            ->from(ModelEntity::class, 'm')
            ->leftJoin('m.brand', 'b');

        // Aplicar filtros
        if (isset($filters['search']) && !empty($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('m.name', ':search'))
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (isset($filters['name']) && !empty($filters['name'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('m.name', ':name'))
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['brandId']) && $filters['brandId'] !== null) {
            $queryBuilder->andWhere('m.brand = :brandId')
                ->setParameter('brandId', $filters['brandId']);
        }

        if (isset($filters['active']) && $filters['active'] !== null) {
            $queryBuilder->andWhere('m.active = :active')
                ->setParameter('active', $filters['active']);
        }

        // Aplicar ordenamiento
        $validSortFields = ['id', 'name', 'active'];
        if (in_array($sortBy, $validSortFields)) {
            $queryBuilder->orderBy('m.' . $sortBy, $sortOrder);
        }

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $queryBuilder->setFirstResult($offset)->setMaxResults($perPage);

        $entities = $queryBuilder->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?ModelModel
    {
        $entity = $this->entityManager->getRepository(ModelEntity::class)->find($id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(ModelModel $model): ModelModel
    {
        return $this->saveModel($model);
    }

    public function update(ModelModel $model): ModelModel
    {
        return $this->saveModel($model);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->getRepository(ModelEntity::class)->find($id);
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
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('COUNT(m.id)')
            ->from(ModelEntity::class, 'm')
            ->where('m.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $queryBuilder->andWhere('m.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int)$queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }

    public function existsByNameAndBrand(string $name, int $brandId, ?int $excludeId = null): bool
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('COUNT(m.id)')
            ->from(ModelEntity::class, 'm')
            ->where('m.name = :name')
            ->andWhere('m.brand = :brandId')
            ->setParameter('name', $name)
            ->setParameter('brandId', $brandId);

        if ($excludeId !== null) {
            $queryBuilder->andWhere('m.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int)$queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }

    public function findByBrandId(int $brandId): array
    {
        $entities = $this->entityManager->getRepository(ModelEntity::class)
            ->findBy(['brand' => $brandId], ['name' => 'ASC']);

        return array_map([$this, 'toDomain'], $entities);
    }

    public function countTotal(array $filters = []): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('COUNT(m.id)')
            ->from(ModelEntity::class, 'm')
            ->leftJoin('m.brand', 'b');

        // Aplicar los mismos filtros que en findAll
        if (isset($filters['search']) && !empty($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('m.name', ':search'))
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (isset($filters['name']) && !empty($filters['name'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('m.name', ':name'))
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['brandId']) && $filters['brandId'] !== null) {
            $queryBuilder->andWhere('m.brand = :brandId')
                ->setParameter('brandId', $filters['brandId']);
        }

        if (isset($filters['active']) && $filters['active'] !== null) {
            $queryBuilder->andWhere('m.active = :active')
                ->setParameter('active', $filters['active']);
        }

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function findActiveByBrandId(int $brandId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m')
            ->from(ModelEntity::class, 'm')
            ->where('m.brand = :brandId')
            ->andWhere('m.active = true')
            ->setParameter('brandId', $brandId)
            ->orderBy('m.name', 'ASC');

        $entities = $queryBuilder->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }
}