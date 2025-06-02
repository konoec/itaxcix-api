<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserStatusModel;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserStatusEntity;

class DoctrineUserStatusRepository implements UserStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(UserStatusEntity $entity): UserStatusModel {
        return new UserStatusModel(
            name: $entity->getName(),
            id: $entity->getId(),
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
}