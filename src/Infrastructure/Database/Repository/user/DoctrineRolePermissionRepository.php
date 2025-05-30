<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\RolePermissionModel;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\RolePermissionEntity;

class DoctrineRolePermissionRepository implements RolePermissionRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(RolePermissionEntity $entity): RolePermissionModel {
        return new RolePermissionModel(
            $entity->getId(),
            $entity->getRole(),
            $entity->getPermission(),
            $entity->isActive()
        );
    }

    public function findPermissionsByRoleId(int $roleId, bool $web): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('rp')
            ->from(RolePermissionEntity::class, 'rp')
            ->where('rp.role = :roleId')
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