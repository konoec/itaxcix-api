<?php

namespace itaxcix\Infrastructure\Database\Repository\person;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use itaxcix\Core\Domain\person\DocumentTypeModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;

class DoctrineDocumentTypeRepository implements DocumentTypeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DocumentTypeModel $entity): DocumentTypeModel {
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
            ->from(DocumentTypeModel::class, 'dt')
            ->where('dt.name = :name')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}