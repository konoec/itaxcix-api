<?php

namespace itaxcix\Shared\DTO\useCases\InfractionReport;

class InfractionReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?int $userId = null,
        public readonly ?int $severityId = null,
        public readonly ?int $statusId = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly ?string $description = null,
        public readonly ?string $sortBy = 'id',
        public readonly ?string $sortDirection = 'DESC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            userId: isset($data['userId']) ? (int)$data['userId'] : null,
            severityId: isset($data['severityId']) ? (int)$data['severityId'] : null,
            statusId: isset($data['statusId']) ? (int)$data['statusId'] : null,
            dateFrom: $data['dateFrom'] ?? null,
            dateTo: $data['dateTo'] ?? null,
            description: $data['description'] ?? null,
            sortBy: $data['sortBy'] ?? 'id',
            sortDirection: strtoupper($data['sortDirection'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        );
    }
}

