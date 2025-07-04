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

    public function findTravelsByUserId(int $userId, int $page, int $perPage): array
    {
        // Cambiar esta línea para que sea consistente con el handler
        $offset = ($page - 1) * $perPage;

        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TravelEntity::class, 't')
            ->where('t.citizen = :userId OR t.driver = :userId')
            ->setParameter('userId', $userId)
            ->setFirstResult($offset)  // Usar la variable calculada
            ->setMaxResults($perPage)
            ->getQuery();

        $entities = $query->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function countTravelsByUserId(int $userId): int
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(TravelEntity::class, 't')
            ->where('t.citizen = :userId OR t.driver = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findReport(\itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('t, citizen, driver, origin, destination, status')
            ->from(TravelEntity::class, 't')
            ->leftJoin('t.citizen', 'citizen')
            ->leftJoin('t.driver', 'driver')
            ->leftJoin('t.origin', 'origin')
            ->leftJoin('t.destination', 'destination')
            ->leftJoin('t.status', 'status');

        if ($dto->startDate) {
            $qb->andWhere('t.startDate >= :startDate')
                ->setParameter('startDate', $dto->startDate . ' 00:00:00');
        }
        if ($dto->endDate) {
            $qb->andWhere('t.endDate <= :endDate')
                ->setParameter('endDate', $dto->endDate . ' 23:59:59');
        }
        if ($dto->citizenId) {
            $qb->andWhere('citizen.id = :citizenId')
                ->setParameter('citizenId', $dto->citizenId);
        }
        if ($dto->driverId) {
            $qb->andWhere('driver.id = :driverId')
                ->setParameter('driverId', $dto->driverId);
        }
        if ($dto->statusId) {
            $qb->andWhere('status.id = :statusId')
                ->setParameter('statusId', $dto->statusId);
        }
        if ($dto->origin) {
            $qb->andWhere('origin.name LIKE :origin')
                ->setParameter('origin', '%' . $dto->origin . '%');
        }
        if ($dto->destination) {
            $qb->andWhere('destination.name LIKE :destination')
                ->setParameter('destination', '%' . $dto->destination . '%');
        }
        $sortBy = in_array($dto->sortBy, ['creationDate','startDate','endDate']) ? 't.' . $dto->sortBy : 't.creationDate';
        $sortDirection = strtoupper($dto->sortDirection) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortBy, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof TravelEntity) {
                $result[] = [
                    'id' => $entity->getId(),
                    'citizenName' => $entity->getCitizen()?->getPerson()?->getName() ?? null,
                    'driverName' => $entity->getDriver()?->getPerson()?->getName() ?? null,
                    'origin' => $entity->getOrigin()?->getName() ?? null,
                    'destination' => $entity->getDestination()?->getName() ?? null,
                    'startDate' => $entity->getStartDate()?->format('Y-m-d H:i:s'),
                    'endDate' => $entity->getEndDate()?->format('Y-m-d H:i:s'),
                    'creationDate' => $entity->getCreationDate()->format('Y-m-d H:i:s'),
                    'status' => $entity->getStatus()?->getName() ?? null
                ];
            }
        }
        return $result;
    }

    public function countReport(\itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(TravelEntity::class, 't')
            ->leftJoin('t.citizen', 'citizen')
            ->leftJoin('t.driver', 'driver')
            ->leftJoin('t.origin', 'origin')
            ->leftJoin('t.destination', 'destination')
            ->leftJoin('t.status', 'status');

        if ($dto->startDate) {
            $qb->andWhere('t.startDate >= :startDate')
                ->setParameter('startDate', $dto->startDate . ' 00:00:00');
        }
        if ($dto->endDate) {
            $qb->andWhere('t.endDate <= :endDate')
                ->setParameter('endDate', $dto->endDate . ' 23:59:59');
        }
        if ($dto->citizenId) {
            $qb->andWhere('citizen.id = :citizenId')
                ->setParameter('citizenId', $dto->citizenId);
        }
        if ($dto->driverId) {
            $qb->andWhere('driver.id = :driverId')
                ->setParameter('driverId', $dto->driverId);
        }
        if ($dto->statusId) {
            $qb->andWhere('status.id = :statusId')
                ->setParameter('statusId', $dto->statusId);
        }
        if ($dto->origin) {
            $qb->andWhere('origin.name LIKE :origin')
                ->setParameter('origin', '%' . $dto->origin . '%');
        }
        if ($dto->destination) {
            $qb->andWhere('destination.name LIKE :destination')
                ->setParameter('destination', '%' . $dto->destination . '%');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}