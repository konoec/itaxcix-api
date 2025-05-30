<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\ContactTypeModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;

class DoctrineContactTypeRepository implements ContactTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(ContactTypeEntity $entity): ContactTypeModel {
        return new ContactTypeModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findContactTypeById(int $id): ?ContactTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContactTypeEntity::class, 'ct')
            ->where('ct.id = :id')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}