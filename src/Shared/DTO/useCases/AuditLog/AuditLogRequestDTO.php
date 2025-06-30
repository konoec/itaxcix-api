<?php

namespace itaxcix\Shared\DTO\useCases\AuditLog;

class AuditLogRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $affectedTable = null,
        public readonly ?string $operation = null,
        public readonly ?string $systemUser = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly ?string $sortBy = 'date',
        public readonly ?string $sortDirection = 'DESC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            affectedTable: $data['affectedTable'] ?? null,
            operation: $data['operation'] ?? null,
            systemUser: $data['systemUser'] ?? null,
            dateFrom: $data['dateFrom'] ?? null,
            dateTo: $data['dateTo'] ?? null,
            sortBy: $data['sortBy'] ?? 'date',
            sortDirection: strtoupper($data['sortDirection'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        );
    }
}

