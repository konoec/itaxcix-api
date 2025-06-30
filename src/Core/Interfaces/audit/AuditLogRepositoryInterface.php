<?php

namespace itaxcix\Core\Interfaces\audit;

use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogRequestDTO;

interface AuditLogRepositoryInterface
{
    public function findAuditLogs(AuditLogRequestDTO $dto, bool $paginated = true): array;
    public function countAuditLogs(AuditLogRequestDTO $dto): int;
    public function findAuditLogById(int $id): ?array;
}

