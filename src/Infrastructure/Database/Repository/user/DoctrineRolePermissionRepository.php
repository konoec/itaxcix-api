<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\RolePermissionModel;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
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

    public function findPermissionsByRoleId(int $roleId, bool $web): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('rp')
            ->from(RolePermissionEntity::class, 'rp')
            ->join('rp.role', 'r')
            ->join('rp.permission', 'p')
            ->where('rp.role = :roleId')
            ->andWhere('rp.active = :active')
            ->andWhere('r.web = :web')
            ->andWhere('p.web = :web')
            ->setParameter('roleId', $roleId)
            ->setParameter('active', true)
            ->setParameter('web', $web)
            ->getQuery();

        $result = $query->getResult();

        return array_map(function ($item) {
            return $this->toDomain($item);
        }, $result);
    }
}