<?php

namespace itaxcix\Infrastructure\Database\Repository\person;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\person\DocumentTypeModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\person\DocumentTypeEntity;

class DoctrineDocumentTypeRepository implements DocumentTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DocumentTypeEntity $entity): DocumentTypeModel {
        return new DocumentTypeModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findDocumentTypeByName(string $name): ?DocumentTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('dt')
            ->from(DocumentTypeEntity::class, 'dt')
            ->where('dt.name = :name')
            ->andWhere('dt.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}