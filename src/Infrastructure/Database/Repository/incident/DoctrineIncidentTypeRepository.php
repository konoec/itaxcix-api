<?php

namespace itaxcix\Infrastructure\Database\Repository\incident;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;

class DoctrineIncidentTypeRepository implements IncidentTypeRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(IncidentTypeEntity $entity): IncidentTypeModel {
        return new IncidentTypeModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findIncidentTypeByName(string $name): ?IncidentTypeModel {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(IncidentTypeEntity::class, 't')
            ->where('t.name = :name')
            ->andWhere('t.active = true')
            ->setParameter('name', $name)
            ->getQuery();
        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }
}

