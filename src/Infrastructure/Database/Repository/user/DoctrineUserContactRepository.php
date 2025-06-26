<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineUserContactRepository implements UserContactRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContactTypeRepositoryInterface $contactTypeRepository,
        UserRepositoryInterface $userRepository,
    ){{
        $this->entityManager = $entityManager;
        $this->contactTypeRepository = $contactTypeRepository;
        $this->userRepository = $userRepository;
    }
    }

    public function toDomain(UserContactEntity $entity): UserContactModel
    {
        return new UserContactModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            type: $this->contactTypeRepository->toDomain($entity->getType()),
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

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
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


    public function findUserContactByUserId(int $userId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.active = :active')
            ->andWhere('uc.confirmed = :confirmed')
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->setParameter('confirmed', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserContactByUserIdAndContactTypeId(int $userId, int $contactTypeId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.type = :contactTypeId')
            ->andWhere('uc.active = :active')
            ->andWhere('uc.confirmed = :confirmed')
            ->setParameter('userId', $userId)
            ->setParameter('contactTypeId', $contactTypeId)
            ->setParameter('active', true)
            ->setParameter('confirmed', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }
}
