<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\PermissionModel;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
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
            active: $entity->isActive()
        );
    }
}