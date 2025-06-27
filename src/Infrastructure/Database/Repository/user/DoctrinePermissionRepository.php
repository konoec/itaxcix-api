<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\PermissionModel;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\DriverProfileEntity;
use itaxcix\Infrastructure\Database\Entity\user\PermissionEntity;

class DoctrinePermissionRepository implements PermissionRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(PermissionEntity $entity): PermissionModel {
        return new PermissionModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive(),
            web: $entity->isWeb()
        );
    }

    public function findPermissionById(int $id): ?PermissionModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result ? $this->toDomain($result) : null;
    }

    public function findPermissionByName(string $name): ?PermissionModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result ? $this->toDomain($result) : null;
    }

    public function findAllPermissions(): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p');
        $results = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $results);
    }

    public function findAllPermissionsPaginated(int $page, int $perPage): object
    {
        // Contar total de registros
        $totalQb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(PermissionEntity::class, 'p');
        $total = (int) $totalQb->getQuery()->getSingleScalarResult();

        // Calcular offset y lastPage
        $offset = ($page - 1) * $perPage;
        $lastPage = ceil($total / $perPage);

        // Obtener registros paginados
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->setFirstResult($offset)
            ->setMaxResults($perPage);

        $results = $qb->getQuery()->getResult();
        $items = array_map([$this, 'toDomain'], $results);

        // Crear metadatos de paginación
        $meta = new \itaxcix\Shared\DTO\generic\PaginationMetaDTO(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );

        // Retornar objeto con items y meta
        return (object) [
            'items' => $items,
            'meta' => $meta
        ];
    }

    public function savePermission(PermissionModel $permission): PermissionModel
    {
        if ($permission->getId()){
            $entity = $this->entityManager->find(PermissionEntity::class, $permission->getId());
        } else {
            $entity = new PermissionEntity();
        }

        $entity->setName($permission->getName());
        $entity->setActive($permission->isActive());
        $entity->setWeb($permission->isWeb());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function deletePermission(PermissionModel $permission): void
    {
        $entity = $this->entityManager->find(PermissionEntity::class, $permission->getId());
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }
}