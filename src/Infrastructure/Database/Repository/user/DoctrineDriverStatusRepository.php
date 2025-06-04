<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\DriverStatusModel;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\DriverStatusEntity;

class DoctrineDriverStatusRepository implements DriverStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DriverStatusEntity $entity): DriverStatusModel {
        return new DriverStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findDriverStatusByName(string $name): ?DriverStatusModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('ds')
            ->from(DriverStatusEntity::class, 'ds')
            ->where('ds.name = :name')
            ->andWhere('ds.active = true')
            ->setParameter('name', $name)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $this->toDomain($result);
    }
}