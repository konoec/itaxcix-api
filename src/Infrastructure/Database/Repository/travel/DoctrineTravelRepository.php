<?php

namespace itaxcix\Infrastructure\Database\Repository\travel;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Core\Interfaces\location\CoordinatesRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\CoordinatesEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelStatusEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineTravelRepository implements TravelRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private TravelStatusRepositoryInterface $travelStatusRepository;
    private UserRepositoryInterface $userRepository;
    private CoordinatesRepositoryInterface $coordinatesRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TravelStatusRepositoryInterface $travelStatusRepository,
        UserRepositoryInterface $userRepository,
        CoordinatesRepositoryInterface $coordinatesRepository
    ){
        $this->entityManager = $entityManager;
        $this->travelStatusRepository = $travelStatusRepository;
        $this->userRepository = $userRepository;
        $this->coordinatesRepository = $coordinatesRepository;
    }

    public function toDomain(TravelEntity $entity): TravelModel {
        return new TravelModel(
            id: $entity->getId(),
            citizen: $this->userRepository->toDomain($entity->getCitizen()),
            driver: $this->userRepository->toDomain($entity->getDriver()),
            origin: $this->coordinatesRepository->toDomain($entity->getOrigin()),
            destination: $this->coordinatesRepository->toDomain($entity->getDestination()),
            startDate: $entity->getStartDate(),
            endDate: $entity->getEndDate(),
            creationDate: $entity->getCreationDate(),
            status: $this->travelStatusRepository->toDomain($entity->getStatus()),
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveTravel(TravelModel $travelModel): TravelModel
    {
        if ($travelModel->getId()) {
            $entity = $this->entityManager->find(TravelEntity::class, $travelModel->getId());
        } else {
            $entity = new TravelEntity();
        }

        $entity->setCitizen(
            $this->entityManager->getReference(
                UserEntity::class,
                $travelModel->getCitizen()->getId()
            )
        );
        $entity->setDriver(
            $this->entityManager->getReference(
                UserEntity::class,
                $travelModel->getDriver()->getId()
            )
        );
        $entity->setOrigin(
            $this->entityManager->getReference(
                CoordinatesEntity::class,
                $travelModel->getOrigin()->getId()
            )
        );
        $entity->setDestination(
            $this->entityManager->getReference(
                CoordinatesEntity::class,
                $travelModel->getDestination()->getId()
            )
        );
        $entity->setStartDate($travelModel->getStartDate());
        $entity->setEndDate($travelModel->getEndDate());
        $entity->setCreationDate(new DateTime());
        $entity->setStatus(
            $this->entityManager->getReference(
                TravelStatusEntity::class,
                $travelModel->getStatus()->getId()
            )
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findTravelById(int $id): ?TravelModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TravelEntity::class, 't')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}