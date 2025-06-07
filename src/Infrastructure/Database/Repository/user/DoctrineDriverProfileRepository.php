<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\DriverProfileEntity;
use itaxcix\Infrastructure\Database\Entity\user\DriverStatusEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineDriverProfileRepository implements DriverProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private DriverStatusRepositoryInterface $driverStatusRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepositoryInterface $userRepository, DriverStatusRepositoryInterface $driverStatusRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->driverStatusRepository = $driverStatusRepository;
    }

    public function toDomain(DriverProfileEntity $entity): DriverProfileModel {
        return new DriverProfileModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            status: $this->driverStatusRepository->toDomain($entity->getStatus()),
            averageRating: $entity->getAverageRating(),
            ratingCount: $entity->getRatingCount()
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
        $entity->setStatus(
            $this->entityManager->getReference(
                DriverStatusEntity::class, $driverProfileModel->getStatus()->getId()
            )
        );
        $entity->setAverageRating($driverProfileModel->getAverageRating());
        $entity->setRatingCount($driverProfileModel->getRatingCount());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findDriverProfileByUserId(int $userId): ?DriverProfileModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('dp')
            ->from(DriverProfileEntity::class, 'dp')
            ->join('dp.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $this->toDomain($result);
    }

    public function findDriversProfilesByStatusId(int $statusId, int $offset = 0, int $perPage = 20): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('dp')
            ->from(DriverProfileEntity::class, 'dp')
            ->join('dp.status', 'ds')
            ->where('ds.id = :statusId')
            ->setParameter('statusId', $statusId)
            ->setFirstResult($offset)
            ->setMaxResults($perPage);

        $result = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $result);
    }

    public function countDriversProfilesByStatusId(int $statusId): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(dp.id)')
            ->from(DriverProfileEntity::class, 'dp')
            ->join('dp.status', 'ds')
            ->where('ds.id = :statusId')
            ->setParameter('statusId', $statusId);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}