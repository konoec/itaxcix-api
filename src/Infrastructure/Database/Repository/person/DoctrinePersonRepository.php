<?php

namespace itaxcix\Infrastructure\Database\Repository\person;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\person\PersonEntity;

class DoctrinePersonRepository implements PersonRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(PersonEntity $entity): PersonModel
    {
        return new PersonModel(
            id: $entity->getId(),
            name: $entity->getName(),
            lastName: $entity->getLastName(),
            documentType: $entity->getDocumentType(),
            document: $entity->getDocument(),
            validationDate: $entity->getValidationDate(),
            active: $entity->isActive()
        );
    }

    public function findAllPersonByDocument(string $documentValue): ?PersonModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PersonModel::class, 'p')
            ->where('p.document = :documentValue')
            ->setParameter('documentValue', $documentValue)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function savePerson(PersonModel $personModel): PersonModel
    {
        $entity = new PersonEntity();
        $entity->setName($personModel->getName());
        $entity->setLastName($personModel->getLastName());
        $entity->setDocumentType($personModel->getDocumentType());
        $entity->setDocument($personModel->getDocument());
        $entity->setValidationDate($personModel->getValidationDate());
        $entity->setActive($personModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findPersonById(int $personId): ?PersonModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PersonModel::class, 'p')
            ->where('p.id = :personId')
            ->setParameter('personId', $personId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAllPersonById(int $personId): ?PersonModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PersonModel::class, 'p')
            ->where('p.id = :personId')
            ->setParameter('personId', $personId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}