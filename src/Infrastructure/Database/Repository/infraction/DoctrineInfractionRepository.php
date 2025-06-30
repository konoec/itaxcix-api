<?php

namespace itaxcix\Infrastructure\Database\Repository\infraction;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Interfaces\infraction\InfractionRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\infraction\InfractionEntity;

class DoctrineInfractionRepository implements InfractionRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findReport(\itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('i, u, p, s, sev')
            ->from(InfractionEntity::class, 'i')
            ->leftJoin('i.user', 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('i.status', 's')
            ->leftJoin('i.severity', 'sev');

        if ($dto->userId) {
            $qb->andWhere('u.id = :userId')
                ->setParameter('userId', $dto->userId);
        }
        if ($dto->severityId) {
            $qb->andWhere('sev.id = :severityId')
                ->setParameter('severityId', $dto->severityId);
        }
        if ($dto->statusId) {
            $qb->andWhere('s.id = :statusId')
                ->setParameter('statusId', $dto->statusId);
        }
        if ($dto->dateFrom) {
            $qb->andWhere('i.date >= :dateFrom')
                ->setParameter('dateFrom', $dto->dateFrom . ' 00:00:00');
        }
        if ($dto->dateTo) {
            $qb->andWhere('i.date <= :dateTo')
                ->setParameter('dateTo', $dto->dateTo . ' 23:59:59');
        }
        if ($dto->description) {
            $qb->andWhere('i.description LIKE :description')
                ->setParameter('description', '%' . $dto->description . '%');
        }
        $allowedSort = [
            'id' => 'i.id',
            'userId' => 'u.id',
            'severityId' => 'sev.id',
            'statusId' => 's.id',
            'date' => 'i.date'
        ];
        $sortBy = $allowedSort[$dto->sortBy] ?? 'i.id';
        $sortDirection = strtoupper($dto->sortDirection) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortBy, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof InfractionEntity) {
                $user = $entity->getUser();
                $person = $user?->getPerson();
                $severity = $entity->getSeverity();
                $status = $entity->getStatus();
                $result[] = [
                    'id' => $entity->getId(),
                    'userId' => $user?->getId(),
                    'userName' => $person ? trim(($person->getName() ?? '') . ' ' . ($person->getLastName() ?? '')) : null,
                    'severityId' => $severity?->getId(),
                    'severityName' => $severity?->getName(),
                    'statusId' => $status?->getId(),
                    'statusName' => $status?->getName(),
                    'date' => $entity->getDate()->format('Y-m-d H:i:s'),
                    'description' => $entity->getDescription()
                ];
            }
        }
        return $result;
    }

    public function countReport(\itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(i.id)')
            ->from(InfractionEntity::class, 'i')
            ->leftJoin('i.user', 'u')
            ->leftJoin('i.status', 's')
            ->leftJoin('i.severity', 'sev');
        if ($dto->userId) {
            $qb->andWhere('u.id = :userId')
                ->setParameter('userId', $dto->userId);
        }
        if ($dto->severityId) {
            $qb->andWhere('sev.id = :severityId')
                ->setParameter('severityId', $dto->severityId);
        }
        if ($dto->statusId) {
            $qb->andWhere('s.id = :statusId')
                ->setParameter('statusId', $dto->statusId);
        }
        if ($dto->dateFrom) {
            $qb->andWhere('i.date >= :dateFrom')
                ->setParameter('dateFrom', $dto->dateFrom . ' 00:00:00');
        }
        if ($dto->dateTo) {
            $qb->andWhere('i.date <= :dateTo')
                ->setParameter('dateTo', $dto->dateTo . ' 23:59:59');
        }
        if ($dto->description) {
            $qb->andWhere('i.description LIKE :description')
                ->setParameter('description', '%' . $dto->description . '%');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
