<?php

namespace itaxcix\Infrastructure\Database\Repository\person;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\person\DocumentTypeModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\person\DocumentTypeEntity;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypePaginationRequestDTO;

class DoctrineDocumentTypeRepository implements DocumentTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DocumentTypeEntity $entity): DocumentTypeModel {
        return new DocumentTypeModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findDocumentTypeByName(string $name): ?DocumentTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('dt')
            ->from(DocumentTypeEntity::class, 'dt')
            ->where('dt.name = :name')
            ->andWhere('dt.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAll(DocumentTypePaginationRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('dt')
            ->from(DocumentTypeEntity::class, 'dt');

        // Aplicar filtros
        if ($dto->getSearch()) {
            $qb->andWhere('dt.name LIKE :search')
                ->setParameter('search', '%' . $dto->getSearch() . '%');
        }

        if ($dto->getName()) {
            $qb->andWhere('dt.name LIKE :name')
                ->setParameter('name', '%' . $dto->getName() . '%');
        }

        if ($dto->getActive() !== null) {
            $qb->andWhere('dt.active = :active')
                ->setParameter('active', $dto->getActive());
        }

        if ($dto->getOnlyActive()) {
            $qb->andWhere('dt.active = true');
        }

        // Antes de aplicar paginación y ordenamiento
        $totalQuery = clone $qb;
        $totalQuery->resetDQLPart('orderBy');
        $total = $totalQuery->select('COUNT(dt.id)')->getQuery()->getSingleScalarResult();

        // Aplicar ordenamiento
        if ($dto->getSortBy() && in_array($dto->getSortBy(), ['id', 'name', 'active'])) {
            $qb->orderBy('dt.' . $dto->getSortBy(), $dto->getSortDirection());
        }


        // Aplicar paginación
        $qb->setFirstResult(($dto->getPage() - 1) * $dto->getPerPage())
            ->setMaxResults($dto->getPerPage());

        $entities = $qb->getQuery()->getResult();

        $models = array_map([$this, 'toDomain'], $entities);
        $items = array_map(fn($model) => $model->toArray(), $models);

        return [
            'items' => $items,
            'meta' => [
                'total' => (int)$total,
                'perPage' => $dto->getPerPage(),
                'currentPage' => $dto->getPage(),
                'lastPage' => max(1, (int)ceil($total / $dto->getPerPage())),
                'search' => $dto->getSearch(),
                'filters' => [
                    'name' => $dto->getName(),
                    'active' => $dto->getActive(),
                    'onlyActive' => $dto->getOnlyActive()
                ],
                'sortBy' => $dto->getSortBy(),
                'sortDirection' => $dto->getSortDirection()
            ]
        ];
    }

    public function findById(int $id): ?DocumentTypeModel
    {
        $entity = $this->entityManager->find(DocumentTypeEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(DocumentTypeModel $model): DocumentTypeModel
    {
        $entity = $model->toEntity();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(DocumentTypeModel $model): DocumentTypeModel
    {
        $entity = $this->entityManager->find(DocumentTypeEntity::class, $model->getId());

        if (!$entity) {
            throw new \InvalidArgumentException("DocumentType with ID {$model->getId()} not found");
        }

        $entity->setName($model->getName());
        $entity->setActive($model->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(DocumentTypeEntity::class, $id);

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
            ->select('COUNT(dt.id)')
            ->from(DocumentTypeEntity::class, 'dt')
            ->where('dt.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('dt.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}