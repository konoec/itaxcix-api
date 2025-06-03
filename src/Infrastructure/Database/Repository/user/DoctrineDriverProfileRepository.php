<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\DriverProfileEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineDriverProfileRepository implements DriverProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepositoryInterface $userRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function toDomain(DriverProfileEntity $entity): DriverProfileModel {
        return new DriverProfileModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            available: $entity->isAvailable()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveDriverProfile(DriverProfileModel $driverProfileModel): DriverProfileModel
    {
        if ($driverProfileModel->getId()){
            $entity = $this->entityManager->find(DriverProfileEntity::class, $driverProfileModel->getId());
        } else {
            $entity = new DriverProfileEntity();
        }

        $entity->setUser(
            $this->entityManager->getReference(
                UserEntity::class, $driverProfileModel->getUser()->getId()
            )
        );
        $entity->setAvailable($driverProfileModel->isAvailable());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}