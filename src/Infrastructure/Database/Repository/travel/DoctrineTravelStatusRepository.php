<?php

namespace itaxcix\Infrastructure\Database\Repository\travel;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\travel\TravelStatusModel;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\travel\TravelStatusEntity;

class DoctrineTravelStatusRepository implements TravelStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    public function toDomain(TravelStatusEntity $entity): TravelStatusModel {
        return new TravelStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findTravelStatusByName(string $name): ?TravelStatusModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ts')
            ->from(TravelStatusEntity::class, 'ts')
            ->where('ts.name = :name')
            ->andWhere('ts.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}