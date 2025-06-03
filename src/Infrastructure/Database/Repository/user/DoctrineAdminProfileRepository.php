<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\user\AdminProfileModel;
use itaxcix\Core\Interfaces\user\AdminProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\AdminProfileEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineAdminProfileRepository implements AdminProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepositoryInterface $userRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function toDomain(AdminProfileEntity $entity): AdminProfileModel {
        return new AdminProfileModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            area: $entity->getArea(),
            position: $entity->getPosition()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveAdminProfile(AdminProfileModel $adminProfileModel): AdminProfileModel
    {
        if ($adminProfileModel->getId()){
            $entity = $this->entityManager->find(AdminProfileEntity::class, $adminProfileModel->getId());
        } else {
            $entity = new AdminProfileEntity();
        }

        $entity->setUser(
            $this->entityManager->getReference(
                UserEntity::class, $adminProfileModel->getUser()->getId()
            )
        );
        $entity->setArea($adminProfileModel->getArea());
        $entity->setPosition($adminProfileModel->getPosition());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}