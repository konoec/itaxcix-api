<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserCodeTypeModel;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeTypeEntity;

class DoctrineUserCodeTypeRepository implements UserCodeTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(UserCodeTypeEntity $entity): UserCodeTypeModel {
        return new UserCodeTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findUserCodeTypeByName(string $name): ?UserCodeTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uct')
            ->from(UserCodeTypeEntity::class, 'uct')
            ->where('uct.name = :name')
            ->andWhere('uct.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}