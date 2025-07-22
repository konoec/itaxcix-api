<?php

namespace itaxcix\Infrastructure\Database\Repository\person;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\person\DocumentTypeEntity;
use itaxcix\Infrastructure\Database\Entity\person\PersonEntity;

class DoctrinePersonRepository implements PersonRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private DocumentTypeRepositoryInterface $documentTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, DocumentTypeRepositoryInterface $documentTypeRepository) {
        $this->entityManager = $entityManager;
        $this->documentTypeRepository = $documentTypeRepository;
    }

    public function toDomain(PersonEntity $entity): PersonModel
    {
        return new PersonModel(
            id: $entity->getId(),
            name: $entity->getName(),
            lastName: $entity->getLastName(),
            documentType: $this->documentTypeRepository->toDomain($entity->getDocumentType()),
            document: $entity->getDocument(),
            validationDate: $entity->getValidationDate(),
            image: $entity->getImage(),
            active: $entity->isActive()
        );
    }

    public function findAllPersonByDocument(string $documentValue, int $documentTypeId): ?PersonModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PersonEntity::class, 'p')
            ->where('p.document = :documentValue')
            ->andWhere('p.documentType = :documentTypeId')
            ->setParameter('documentValue', $documentValue)
            ->setParameter('documentTypeId', $documentTypeId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function savePerson(PersonModel $personModel): PersonModel
    {
        if ($personModel->getId()) {
            $entity = $this->entityManager->find(PersonEntity::class, $personModel->getId());
        } else {
            $entity = new PersonEntity();
        }

        $entity->setName($personModel->getName());
        $entity->setLastName($personModel->getLastName());
        $entity->setDocumentType(
            $this->entityManager->getReference(
                DocumentTypeEntity::class,
                $personModel->getDocumentType()->getId()
            )
        );
        $entity->setDocument($personModel->getDocument());
        $entity->setValidationDate($personModel->getValidationDate());
        $entity->setActive($personModel->isActive());
        $entity->setImage($personModel->getImage());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        error_log('[DoctrinePersonRepository] Persona persistida con ID: ' . $entity->getId());

        return $this->toDomain($entity);
    }

    public function findPersonById(int $personId): ?PersonModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PersonEntity::class, 'p')
            ->where('p.id = :personId')
            ->andWhere('p.active = :active')
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
            ->from(PersonEntity::class, 'p')
            ->where('p.id = :personId')
            ->setParameter('personId', $personId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findByDocumentTypeId(int $documentTypeId): bool
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(PersonEntity::class, 'p')
            ->where('p.documentType = :documentTypeId')
            ->andWhere('p.active = true')
            ->setParameter('documentTypeId', $documentTypeId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? true : false;
    }
}