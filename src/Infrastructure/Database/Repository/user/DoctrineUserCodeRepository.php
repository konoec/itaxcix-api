<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeEntity;

class DoctrineUserCodeRepository implements UserCodeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(UserCodeEntity $entity): UserCodeModel {
        return new UserCodeModel(
            id: $entity->getId(),
            type: $entity->getType(),
            contact: $entity->getContact(),
            code: $entity->getCode(),expirationDate:
            $entity->getExpirationDate(),
            useDate: $entity->getUseDate(),
            used: $entity->isUsed()
        );
    }

    public function saveUserCode(UserCodeModel $userCodeModel): UserCodeModel
    {
        $entity = new UserCodeEntity();
        $entity->setId($userCodeModel->getId());
        $entity->setType($userCodeModel->getType());
        $entity->setContact($userCodeModel->getContact());
        $entity->setCode($userCodeModel->getCode());
        $entity->setExpirationDate($userCodeModel->getExpirationDate());
        $entity->setUseDate($userCodeModel->getUseDate());
        $entity->setUsed($userCodeModel->isUsed());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}