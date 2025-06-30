<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\ContactTypeModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;

class DoctrineContactTypeRepository implements ContactTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ContactTypeEntity $entity): ContactTypeModel
    {
        return new ContactTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    // Implementación de los métodos requeridos por la interfaz
    public function findAll(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.active = :active')
            ->setParameter('active', true)
            ->orderBy('ct.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map(fn($entity) => $this->toDomain($entity), $entities);
    }

    public function findById(int $id): ?ContactTypeModel
    {
        return $this->findContactTypeById($id);
    }

    public function create(ContactTypeModel $contactType): ContactTypeModel
    {
        $entity = new ContactTypeEntity();
        $entity->setName($contactType->getName());
        $entity->setActive($contactType->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(ContactTypeModel $contactType): ContactTypeModel
    {
        $entity = $this->entityManager->find(ContactTypeEntity::class, $contactType->getId());

        if (!$entity) {
            throw new \RuntimeException("ContactType with id {$contactType->getId()} not found");
        }

        $entity->setName($contactType->getName());
        $entity->setActive($contactType->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(ContactTypeEntity::class, $id);

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
            ->select('COUNT(ct.id)')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.name = :name')
            ->andWhere('ct.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true);

        if ($excludeId !== null) {
            $qb->andWhere('ct.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findWithFilters(array $filters = [], array $orderBy = [], int $limit = 15, int $offset = 0): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct');

        // Aplicar filtros
        if (isset($filters['name'])) {
            $qb->andWhere('LOWER(ct.name) LIKE :name')
               ->setParameter('name', '%' . strtolower($filters['name']) . '%');
        }

        if (isset($filters['active'])) {
            $qb->andWhere('ct.active = :active')
               ->setParameter('active', $filters['active']);
        } else {
            // Por defecto solo mostrar activos
            $qb->andWhere('ct.active = :active')
               ->setParameter('active', true);
        }

        // Contar total
        $countQb = clone $qb;
        $countQb->select('COUNT(ct.id)');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        // Aplicar ordenamiento
        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                $qb->addOrderBy("ct.{$field}", $direction);
            }
        } else {
            $qb->orderBy('ct.name', 'ASC');
        }

        // Aplicar paginación
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $entities = $qb->getQuery()->getResult();
        $data = array_map(fn($entity) => $this->toDomain($entity), $entities);

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    // Métodos existentes
    public function findContactTypeByName(string $name): ?ContactTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.name = :name')
            ->andWhere('ct.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findContactTypeById(int $id): ?ContactTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.id = :id')
            ->andWhere('ct.active = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }
}
