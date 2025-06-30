<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\user\PermissionModel;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Domain\user\RolePermissionModel;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\PermissionEntity;
use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;
use itaxcix\Infrastructure\Database\Entity\user\RolePermissionEntity;

class DoctrineRolePermissionRepository implements RolePermissionRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private RoleRepositoryInterface $roleRepository;
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(EntityManagerInterface $entityManager, RoleRepositoryInterface $roleRepository, PermissionRepositoryInterface $permissionRepository) {
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    private function toDomain(RolePermissionEntity $entity): RolePermissionModel {
        return new RolePermissionModel(
            id: $entity->getId(),
            role: $this->roleRepository->toDomain($entity->getRole()),
            permission: $this->permissionRepository->toDomain($entity->getPermission()),
            active: $entity->isActive()
        );
    }

    public function findRolePermissionById(int $id): ?RolePermissionModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('rp')
            ->from(RolePermissionEntity::class, 'rp')
            ->where('rp.id = :id')
            ->andWhere('rp.active = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findByRoleAndPermission(int $roleId, int $permissionId): ?RolePermissionModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('rp')
            ->from(RolePermissionEntity::class, 'rp')
            ->where('rp.role = :roleId')
            ->andWhere('rp.permission = :permissionId')
            ->andWhere('rp.active = :active')
            ->setParameter('roleId', $roleId)
            ->setParameter('permissionId', $permissionId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAllRolePermissions(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('rp')
            ->from(RolePermissionEntity::class, 'rp')
            ->where('rp.active = :active')
            ->setParameter('active', true)
            ->getQuery();

        $results = $query->getResult();
        return array_map([$this, 'toDomain'], $results);
    }

    public function hasActiveRolesByPermissionId(int $permissionId): bool
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('COUNT(rp.id)')
            ->from(RolePermissionEntity::class, 'rp')
            ->where('rp.permission = :permissionId')
            ->andWhere('rp.active = :active')
            ->setParameter('permissionId', $permissionId)
            ->setParameter('active', true)
            ->getQuery();

        return ($query->getSingleScalarResult() > 0);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveRolePermission(RolePermissionModel $rolePermission): RolePermissionModel
    {
        if ($rolePermission->getId()) {
            $entity = $this->entityManager->find(RolePermissionEntity::class, $rolePermission->getId());
        } else {
            $entity = new RolePermissionEntity();
        }

        $entity->setRole(
            $this->entityManager->getReference(RoleEntity::class, $rolePermission->getRole()->getId())
        );
        $entity->setPermission(
            $this->entityManager->getReference(PermissionEntity::class, $rolePermission->getPermission()->getId())
        );
        $entity->setActive($rolePermission->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function deleteRolePermission(RolePermissionModel $rolePermission): void
    {
        $entity = $this->entityManager->find(RolePermissionEntity::class, $rolePermission->getId());
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }

    // Nuevos métodos para administración avanzada
    public function removeAllByRoleId(int $roleId): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->update(RolePermissionEntity::class, 'rp')
            ->set('rp.active', ':active')
            ->where('rp.role = :roleId')
            ->setParameter('active', false)
            ->setParameter('roleId', $roleId);

        $qb->getQuery()->execute();
    }

    public function assignPermissionToRole(
        RoleModel $role,
        PermissionModel $permission
    ): RolePermissionModel {
        $entity = new RolePermissionEntity();

        $entity->setRole(
            $this->entityManager->getReference(RoleEntity::class, $role->getId())
        );
        $entity->setPermission(
            $this->entityManager->getReference(PermissionEntity::class, $permission->getId())
        );
        $entity->setActive(true);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findPermissionsByRoleId(int $roleId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PermissionEntity::class, 'p')
            ->join(RolePermissionEntity::class, 'rp', 'WITH', 'rp.permission = p.id')
            ->where('rp.role = :roleId')
            ->andWhere('rp.active = :active')
            ->andWhere('p.active = :permissionActive')
            ->setParameter('roleId', $roleId)
            ->setParameter('active', true)
            ->setParameter('permissionActive', true)
            ->getQuery();

        $results = $query->getResult();

        return array_map(function (PermissionEntity $entity) {
            return $this->permissionRepository->toDomain($entity);
        }, $results);
    }
}