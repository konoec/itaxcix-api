<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineUserContactRepository implements UserContactRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepositoryInterface $userRepository, ContactTypeRepositoryInterface $contactTypeRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function toDomain(UserContactEntity $entity): UserContactModel {
        return new UserContactModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            type: $this->contactTypeRepository->toDomain($entity->getType()),
            value: $entity->getValue(),
            confirmed: $entity->isConfirmed(),
            active: $entity->isActive()
        );
    }

    /**
     * @throws ORMException
     */
    public function saveUserContact(UserContactModel $userContactModel): UserContactModel
    {
        // 1. Si tiene id, buscar entidad; si no, crear nueva
        if ($userContactModel->getId()) {
            $entity = $this->entityManager->find(UserContactEntity::class, $userContactModel->getId());
        } else {
            $entity = new UserContactEntity();
        }

        // 2. Asignar/actualizar campos
        $entity->setUser(
            $this->entityManager->getReference(
                UserEntity::class, $userContactModel->getUser()->getId()
            )
        );
        $entity->setType(
            $this->entityManager->getReference(
                ContactTypeEntity::class, $userContactModel->getType()->getId()
            )
        );
        $entity->setValue($userContactModel->getValue());
        $entity->setConfirmed($userContactModel->isConfirmed());
        $entity->setActive($userContactModel->isActive());

        // 3. Persistir y confirmar
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAllUserContactByValue(string $value): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.value = :value')
            ->setParameter('value', $value)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}