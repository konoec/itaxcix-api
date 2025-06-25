<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\ContactTypeModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;

class DoctrineContactTypeRepository implements ContactTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ContactTypeEntity $entity): ContactTypeModel
    {
        return new ContactTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findContactTypeByName(string $name): ?ContactTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.name = :name')
            ->andWhere('ct.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findContactTypeById(int $id): ?ContactTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.id = :id')
            ->andWhere('ct.active = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }
}
