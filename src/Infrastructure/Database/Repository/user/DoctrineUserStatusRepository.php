<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\user\UserStatusModel;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserStatusEntity;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusPaginationRequestDTO;

class DoctrineUserStatusRepository implements UserStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function toDomain(UserStatusEntity $entity): UserStatusModel {
        return new UserStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findUserStatusByName(string $name): ?UserStatusModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('us')
            ->from(UserStatusEntity::class, 'us')
            ->where('us.name = :name')
            ->andWhere('us.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserStatusById(int $id): ?UserStatusModel
    {
        $entity = $this->entityManager->getRepository(UserStatusEntity::class)->find($id);
        if (!$entity || !$entity->isActive()) {
            return null;
        }
        return $this->toDomain($entity);
    }

    public function findAll(UserStatusPaginationRequestDTO $request): array
    {
        $qb = $this->createQueryBuilder($request);

        $qb->setFirstResult(($request->getPage() - 1) * $request->getPerPage())
           ->setMaxResults($request->getPerPage());

        $entities = $qb->getQuery()->getResult();

        return array_map(fn(UserStatusEntity $entity) => $this->toDomain($entity), $entities);
    }

    public function findById(int $id): ?UserStatusModel
    {
        $entity = $this->entityManager->find(UserStatusEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(UserStatusModel $userStatus): UserStatusModel
    {
        $entity = $userStatus->toEntity();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $userStatus->setId($entity->getId());
        return $userStatus;
    }

    public function update(UserStatusModel $userStatus): UserStatusModel
    {
        $entity = $this->entityManager->find(UserStatusEntity::class, $userStatus->getId());
        if (!$entity) {
            throw new \InvalidArgumentException('UserStatus not found');
        }

        $entity->setName($userStatus->getName());
        $entity->setActive($userStatus->isActive());

        $this->entityManager->flush();
        return $userStatus;
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(UserStatusEntity::class, $id);
        if (!$entity) {
            return false;
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
        return true;
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(us.id)')
            ->from(UserStatusEntity::class, 'us')
            ->where('us.name = :name')
            ->setParameter('name', $name);

        if ($excludeId !== null) {
            $qb->andWhere('us.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function count(UserStatusPaginationRequestDTO $request): int
    {
        // Crear un QueryBuilder separado para el conteo (sin ORDER BY)
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(us.id)')
            ->from(UserStatusEntity::class, 'us');

        // Aplicar solo los filtros (sin ordenamiento)
        if ($request->getSearch()) {
            $qb->andWhere('us.name LIKE :search')
               ->setParameter('search', '%' . $request->getSearch() . '%');
        }

        if ($request->getName()) {
            $qb->andWhere('us.name LIKE :name')
               ->setParameter('name', '%' . $request->getName() . '%');
        }

        if ($request->getActive() !== null) {
            $qb->andWhere('us.active = :active')
               ->setParameter('active', $request->getActive());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function createQueryBuilder(UserStatusPaginationRequestDTO $request): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('us')
            ->from(UserStatusEntity::class, 'us');

        // Filtro por búsqueda global
        if ($request->getSearch()) {
            $qb->andWhere('us.name LIKE :search')
               ->setParameter('search', '%' . $request->getSearch() . '%');
        }

        // Filtro por nombre
        if ($request->getName()) {
            $qb->andWhere('us.name LIKE :name')
               ->setParameter('name', '%' . $request->getName() . '%');
        }

        // Filtro por estado activo
        if ($request->getActive() !== null) {
            $qb->andWhere('us.active = :active')
               ->setParameter('active', $request->getActive());
        }

        // Ordenamiento
        if ($request->getSortBy()) {
            $direction = $request->getSortDirection() === 'desc' ? 'DESC' : 'ASC';
            $qb->orderBy('us.' . $request->getSortBy(), $direction);
        } else {
            $qb->orderBy('us.id', 'DESC');
        }

        return $qb;
    }
}