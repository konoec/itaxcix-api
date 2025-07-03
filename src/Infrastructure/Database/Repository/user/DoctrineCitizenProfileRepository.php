<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\user\CitizenProfileModel;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\CitizenProfileEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineCitizenProfileRepository implements CitizenProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    public function __construct(EntityManagerInterface $entityManager, UserRepositoryInterface $userRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function toDomain(CitizenProfileEntity $entity): CitizenProfileModel {
        return new CitizenProfileModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            averageRating: $entity->getAverageRating(),
            ratingCount: $entity->getRatingCount()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveCitizenProfile(CitizenProfileModel $citizenProfileModel): CitizenProfileModel
    {
        if ($citizenProfileModel->getId()) {
            $entity = $this->entityManager->find(CitizenProfileEntity::class, $citizenProfileModel->getId());
        } else {
            $entity = new CitizenProfileEntity();
        }

        $entity->setUser(
            $this->entityManager->getReference(
                UserEntity::class, $citizenProfileModel->getUser()->getId()
            )
        );
        $entity->setAverageRating($citizenProfileModel->getAverageRating());
        $entity->setRatingCount($citizenProfileModel->getRatingCount());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findCitizenProfileByUserId(int $userId): ?CitizenProfileModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('cp')
            ->from(CitizenProfileEntity::class, 'cp')
            ->join('cp.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('cp.id', 'DESC')
            ->setMaxResults(1);

        $entity = $query->getQuery()->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}