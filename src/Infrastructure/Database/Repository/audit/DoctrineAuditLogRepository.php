<?php

namespace itaxcix\Infrastructure\Database\Repository\audit;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Interfaces\audit\AuditLogRepositoryInterface;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogRequestDTO;
use itaxcix\Infrastructure\Database\Entity\audit\AuditEntity;

class DoctrineAuditLogRepository implements AuditLogRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAuditLogs(AuditLogRequestDTO $dto, bool $paginated = true): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AuditEntity::class, 'a');

        if ($dto->affectedTable) {
            $qb->andWhere('a.affectedTable = :affectedTable')
                ->setParameter('affectedTable', $dto->affectedTable);
        }
        if ($dto->operation) {
            $qb->andWhere('a.operation = :operation')
                ->setParameter('operation', $dto->operation);
        }
        if ($dto->systemUser) {
            $qb->andWhere('a.systemUser = :systemUser')
                ->setParameter('systemUser', $dto->systemUser);
        }
        if ($dto->dateFrom) {
            $qb->andWhere('a.date >= :dateFrom')
                ->setParameter('dateFrom', $dto->dateFrom . ' 00:00:00');
        }
        if ($dto->dateTo) {
            $qb->andWhere('a.date <= :dateTo')
                ->setParameter('dateTo', $dto->dateTo . ' 23:59:59');
        }
        $allowedSort = [
            'date' => 'a.date',
            'affectedTable' => 'a.affectedTable',
            'operation' => 'a.operation',
            'systemUser' => 'a.systemUser'
        ];
        $sortBy = $allowedSort[$dto->sortBy] ?? 'a.date';
        $sortDirection = strtoupper($dto->sortDirection) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortBy, $sortDirection);
        if ($paginated) {
            $qb->setFirstResult(($dto->page - 1) * $dto->perPage)
                ->setMaxResults($dto->perPage);
        }
        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof AuditEntity) {
                $result[] = $this->toDomain($entity);
            }
        }
        return $result;
    }

    public function countAuditLogs(AuditLogRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(AuditEntity::class, 'a');
        if ($dto->affectedTable) {
            $qb->andWhere('a.affectedTable = :affectedTable')
                ->setParameter('affectedTable', $dto->affectedTable);
        }
        if ($dto->operation) {
            $qb->andWhere('a.operation = :operation')
                ->setParameter('operation', $dto->operation);
        }
        if ($dto->systemUser) {
            $qb->andWhere('a.systemUser = :systemUser')
                ->setParameter('systemUser', $dto->systemUser);
        }
        if ($dto->dateFrom) {
            $qb->andWhere('a.date >= :dateFrom')
                ->setParameter('dateFrom', $dto->dateFrom . ' 00:00:00');
        }
        if ($dto->dateTo) {
            $qb->andWhere('a.date <= :dateTo')
                ->setParameter('dateTo', $dto->dateTo . ' 23:59:59');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAuditLogById(int $id): ?array
    {
        $entity = $this->entityManager->find(AuditEntity::class, $id);
        if (!$entity) {
            return null;
        }
        return $this->toDomain($entity);
    }

    public function logAuditEvent(string $affectedTable, string $operation, string $systemUser, ?array $previousData = null, ?array $newData = null): void
    {
        $entity = new AuditEntity();
        $entity->setAffectedTable($affectedTable);
        $entity->setOperation($operation);
        $entity->setSystemUser($systemUser);
        $entity->setDate(new \DateTime());
        $entity->setPreviousData($previousData);
        $entity->setNewData($newData);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function toDomain(AuditEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'affectedTable' => $entity->getAffectedTable(),
            'operation' => $entity->getOperation(),
            'systemUser' => $entity->getSystemUser(),
            'date' => $entity->getDate()->format('Y-m-d H:i:s'),
            'previousData' => $entity->getPreviousData(),
            'newData' => $entity->getNewData()
        ];
    }
}
