<?php

namespace itaxcix\Infrastructure\Database\Repository\incident;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Interfaces\incident\IncidentRepositoryInterface;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentEntity;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineIncidentRepository implements IncidentRepositoryInterface {
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private TravelRepositoryInterface $travelRepository;
    private IncidentTypeRepositoryInterface $incidentTypeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository,
        TravelRepositoryInterface $travelRepository,
        IncidentTypeRepositoryInterface $incidentTypeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->travelRepository = $travelRepository;
        $this->incidentTypeRepository = $incidentTypeRepository;
    }

    public function toDomain(IncidentEntity $entity): IncidentModel {
        return new IncidentModel(
            $entity->getId(),
            $this->userRepository->toDomain($entity->getUser()),
            $this->travelRepository->toDomain($entity->getTravel()),
            $this->incidentTypeRepository->toDomain($entity->getType()),
            $entity->getComment(),
            $entity->isActive()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveIncident(IncidentModel $incidentModel): IncidentModel {
        if ($incidentModel->getId()) {
            $entity = $this->entityManager->find(IncidentEntity::class, $incidentModel->getId());
        } else {
            $entity = new IncidentEntity();
        }
        if ($incidentModel->getUser()) {
            $entity->setUser($this->entityManager->getReference(UserEntity::class, $incidentModel->getUser()->getId()));
        }
        if ($incidentModel->getTravel()) {
            $entity->setTravel($this->entityManager->getReference(TravelEntity::class, $incidentModel->getTravel()->getId()));
        }
        if ($incidentModel->getType()) {
            $entity->setType($this->entityManager->getReference(IncidentTypeEntity::class, $incidentModel->getType()->getId()));
        }
        $entity->setComment($incidentModel->getComment());
        $entity->setActive($incidentModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }

    public function findReport(\itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('i, u, p, t')
            ->from(IncidentEntity::class, 'i')
            ->leftJoin('i.user', 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('i.type', 't');

        if ($dto->userId) {
            $qb->andWhere('u.id = :userId')
                ->setParameter('userId', $dto->userId);
        }
        if ($dto->travelId) {
            $qb->andWhere('i.travel = :travelId')
                ->setParameter('travelId', $dto->travelId);
        }
        if ($dto->typeId) {
            $qb->andWhere('t.id = :typeId')
                ->setParameter('typeId', $dto->typeId);
        }
        if ($dto->active !== null) {
            $qb->andWhere('i.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->comment) {
            $qb->andWhere('i.comment LIKE :comment')
                ->setParameter('comment', '%' . $dto->comment . '%');
        }
        $allowedSort = [
            'id' => 'i.id',
            'userId' => 'u.id',
            'travelId' => 'i.travel',
            'typeId' => 't.id',
            'active' => 'i.active'
        ];
        $sortBy = $allowedSort[$dto->sortBy] ?? 'i.id';
        $sortDirection = strtoupper($dto->sortDirection) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortBy, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof IncidentEntity) {
                $user = $entity->getUser();
                $person = $user?->getPerson();
                $type = $entity->getType();
                $result[] = [
                    'id' => $entity->getId(),
                    'userId' => $user?->getId(),
                    'userName' => $person ? trim(($person->getName() ?? '') . ' ' . ($person->getLastName() ?? '')) : null,
                    'travelId' => $entity->getTravel()?->getId(),
                    'typeId' => $type?->getId(),
                    'typeName' => $type?->getName(),
                    'comment' => $entity->getComment(),
                    'active' => $entity->isActive()
                ];
            }
        }
        return $result;
    }

    public function countReport(\itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(i.id)')
            ->from(IncidentEntity::class, 'i')
            ->leftJoin('i.user', 'u')
            ->leftJoin('i.type', 't');
        if ($dto->userId) {
            $qb->andWhere('u.id = :userId')
                ->setParameter('userId', $dto->userId);
        }
        if ($dto->travelId) {
            $qb->andWhere('i.travel = :travelId')
                ->setParameter('travelId', $dto->travelId);
        }
        if ($dto->typeId) {
            $qb->andWhere('t.id = :typeId')
                ->setParameter('typeId', $dto->typeId);
        }
        if ($dto->active !== null) {
            $qb->andWhere('i.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->comment) {
            $qb->andWhere('i.comment LIKE :comment')
                ->setParameter('comment', '%' . $dto->comment . '%');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByUser(\itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('i, u, p, t, tr')
            ->from(IncidentEntity::class, 'i')
            ->leftJoin('i.user', 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('i.type', 't')
            ->leftJoin('i.travel', 'tr')
            ->where('u.id = :userId')
            ->setParameter('userId', $dto->userId);

        if ($dto->travelId) {
            $qb->andWhere('tr.id = :travelId')
                ->setParameter('travelId', $dto->travelId);
        }
        if ($dto->typeId) {
            $qb->andWhere('t.id = :typeId')
                ->setParameter('typeId', $dto->typeId);
        }
        if ($dto->active !== null) {
            $qb->andWhere('i.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->comment) {
            $qb->andWhere('i.comment LIKE :comment')
                ->setParameter('comment', '%' . $dto->comment . '%');
        }

        $allowedSort = [
            'id' => 'i.id',
            'travelId' => 'tr.id',
            'typeId' => 't.id',
            'active' => 'i.active'
        ];
        $sortBy = $allowedSort[$dto->sortBy] ?? 'i.id';
        $sortDirection = strtoupper($dto->sortDirection) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortBy, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof IncidentEntity) {
                $user = $entity->getUser();
                $person = $user?->getPerson();
                $type = $entity->getType();
                $travel = $entity->getTravel();
                $result[] = [
                    'id' => $entity->getId(),
                    'userId' => $user?->getId(),
                    'userName' => $person ? trim(($person->getName() ?? '') . ' ' . ($person->getLastName() ?? '')) : null,
                    'travelId' => $travel?->getId(),
                    'typeId' => $type?->getId(),
                    'typeName' => $type?->getName(),
                    'comment' => $entity->getComment(),
                    'active' => $entity->isActive()
                ];
            }
        }
        return $result;
    }

    public function countByUser(\itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(i.id)')
            ->from(IncidentEntity::class, 'i')
            ->leftJoin('i.user', 'u')
            ->leftJoin('i.type', 't')
            ->leftJoin('i.travel', 'tr')
            ->where('u.id = :userId')
            ->setParameter('userId', $dto->userId);

        if ($dto->travelId) {
            $qb->andWhere('tr.id = :travelId')
                ->setParameter('travelId', $dto->travelId);
        }
        if ($dto->typeId) {
            $qb->andWhere('t.id = :typeId')
                ->setParameter('typeId', $dto->typeId);
        }
        if ($dto->active !== null) {
            $qb->andWhere('i.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->comment) {
            $qb->andWhere('i.comment LIKE :comment')
                ->setParameter('comment', '%' . $dto->comment . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
