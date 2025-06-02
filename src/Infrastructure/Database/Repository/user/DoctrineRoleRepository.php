<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;

class DoctrineRoleRepository implements RoleRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(RoleEntity $entity): RoleModel {
        return new RoleModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findRoleByName(string $name): ?RoleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.name = :name')
            ->andWhere('r.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}