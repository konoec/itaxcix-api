<?php

namespace itaxcix\Infrastructure\Database\Repository\travel;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Core\Interfaces\location\CoordinatesRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\CoordinatesEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelStatusEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO;

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

    public function findTravelsByUserId(int $userId, int $offset, int $perPage): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TravelEntity::class, 't')
            ->where('t.citizen = :userId OR t.driver = :userId')
            ->setParameter('userId', $userId)
            ->setFirstResult($offset)  // Usar directamente el offset recibido
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

    public function findReport(TravelReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('t, citizen, driver, origin, destination, status')
            ->from(TravelEntity::class, 't')
            ->leftJoin('t.citizen', 'citizen')
            ->leftJoin('t.driver', 'driver')
            ->leftJoin('t.origin', 'origin')
            ->leftJoin('t.destination', 'destination')
            ->leftJoin('t.status', 'status');

        // Aquí puedes usar applyFilters si quieres centralizar los filtros también
        // $this->applyFilters($qb, $dto);

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

        // Solo se usa applyOrdering para el orden
        $this->applyOrdering($qb, $dto);

        $qb->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof TravelEntity) {
                $result[] = [
                    'id' => $entity->getId(),
                    'citizenName' => $entity->getCitizen()?->getPerson()?->getName() . ' ' . $entity->getCitizen()?->getPerson()?->getLastName() ?? null,
                    'driverName' => $entity->getDriver()?->getPerson()?->getName() . ' ' . $entity->getDriver()?->getPerson()?->getLastName() ?? null,
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

    private function applyFilters(QueryBuilder $qb, TravelReportRequestDTO $dto): void
    {
        // Filtro por fecha de inicio
        if ($dto->startDate) {
            $qb->andWhere('DATE(t.creationDate) >= :startDate')
               ->setParameter('startDate', $dto->startDate);
        }

        // Filtro por fecha de fin
        if ($dto->endDate) {
            $qb->andWhere('DATE(t.creationDate) <= :endDate')
               ->setParameter('endDate', $dto->endDate);
        }

        // Filtro por ID de ciudadano
        if ($dto->citizenId) {
            $qb->andWhere('c.id = :citizenId')
               ->setParameter('citizenId', $dto->citizenId);
        }

        // Filtro por ID de conductor
        if ($dto->driverId) {
            $qb->andWhere('d.id = :driverId')
               ->setParameter('driverId', $dto->driverId);
        }

        // Filtro por ID de estado
        if ($dto->statusId) {
            $qb->andWhere('ts.id = :statusId')
               ->setParameter('statusId', $dto->statusId);
        }

        // Filtro por origen (búsqueda parcial)
        if ($dto->origin) {
            $qb->andWhere('co.address LIKE :origin')
               ->setParameter('origin', '%' . $dto->origin . '%');
        }

        // Filtro por destino (búsqueda parcial)
        if ($dto->destination) {
            $qb->andWhere('cd.address LIKE :destination')
               ->setParameter('destination', '%' . $dto->destination . '%');
        }
    }

    private function applyOrdering(QueryBuilder $qb, TravelReportRequestDTO $dto): void
    {
        $sortMapping = [
            'creationDate' => 't.creationDate',
            'startDate' => 't.startDate',
            'endDate' => 't.endDate',
            'citizenId' => 'c.id',
            'driverId' => 'd.id',
            'statusId' => 'ts.id'
        ];

        $sortField = $sortMapping[$dto->sortBy] ?? 't.creationDate';
        $qb->orderBy($sortField, $dto->sortDirection);
    }
    public function countReport(TravelReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(TravelEntity::class, 't')
            ->leftJoin('t.citizen', 'c')
            ->leftJoin('t.driver', 'd')
            ->leftJoin('t.origin', 'co')
            ->leftJoin('t.destination', 'cd')
            ->leftJoin('t.status', 'ts');

        $this->applyFilters($qb, $dto);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findActivesByTravelStatusId(int $travelStatusId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TravelEntity::class, 't')
            ->where('t.status = :travelStatusId')
            ->setParameter('travelStatusId', $travelStatusId)
            ->getQuery();

        $entities = $query->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }
}