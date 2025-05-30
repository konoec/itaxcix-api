<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;

class DoctrineUserContactRepository implements UserContactRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(UserContactEntity $entity): UserContactModel {
        return new UserContactModel(
            $entity->getId(),
            $entity->getUser(),
            $entity->getType(),
            $entity->getValue(),
            $entity->isConfirmed(),
            $entity->isActive()
        );
    }

    public function saveUserContact(UserContactModel $userContactModel): UserContactModel
    {
        $entity = new UserContactEntity();
        $entity->setUser($userContactModel->getUser());
        $entity->setType($userContactModel->getType());
        $entity->setValue($userContactModel->getValue());
        $entity->setConfirmed($userContactModel->isConfirmed());
        $entity->setActive($userContactModel->isActive());

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