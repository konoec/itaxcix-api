<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\TucStatusModel;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucStatusEntity;

class DoctrineTucStatusRepository implements TucStatusRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(TucStatusEntity $entity): TucStatusModel
    {
        return new TucStatusModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllTucStatusByName(string $name): ?TucStatusModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TucStatusEntity::class, 't')
            ->where('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveTucStatus(TucStatusModel $tucStatusModel): TucStatusModel
    {
        $entity = new TucStatusEntity();
        $entity->setId($tucStatusModel->getId());
        $entity->setName($tucStatusModel->getName());
        $entity->setActive($tucStatusModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}