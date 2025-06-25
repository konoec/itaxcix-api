<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineUserContactRepository implements UserContactRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function toDomain(UserContactEntity $entity): UserContactModel
    {
        return new UserContactModel(
            id: $entity->getId(),
            user: $entity->getUser(),
            type: $entity->getType(),
            value: $entity->getValue(),
            confirmed: $entity->isConfirmed(),
            active: $entity->isActive()
        );
    }

    public function findAllUserContactByValue(string $value): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.value = :value')
            ->andWhere('uc.active = :active')
            ->setParameter('value', $value)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserContactByTypeAndUser(int $typeId, int $userId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.type = :typeId')
            ->andWhere('uc.user = :userId')
            ->andWhere('uc.active = :active')
            ->setParameter('typeId', $typeId)
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveUserContact(UserContactModel $contact): UserContactModel
    {
        if ($contact->getId()) {
            $entity = $this->entityManager->find(UserContactEntity::class, $contact->getId());
        } else {
            $entity = new UserContactEntity();
        }

        $entity->setUser(
            $this->entityManager->getReference(UserEntity::class, $contact->getUser()->getId())
        );
        $entity->setType(
            $this->entityManager->getReference(ContactTypeEntity::class, $contact->getType()->getId())
        );
        $entity->setValue($contact->getValue());
        $entity->setConfirmed($contact->isConfirmed());
        $entity->setActive($contact->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findActiveContactByUserAndType(int $userId, int $contactTypeId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.type = :typeId')
            ->andWhere('uc.active = :active')
            ->setParameter('userId', $userId)
            ->setParameter('typeId', $contactTypeId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function deleteContact(int $contactId): void
    {
        $entity = $this->entityManager->find(UserContactEntity::class, $contactId);
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }
}
