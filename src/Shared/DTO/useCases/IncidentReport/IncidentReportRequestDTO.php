<?php

namespace itaxcix\Shared\DTO\useCases\IncidentReport;

class IncidentReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?int $userId = null,
        public readonly ?int $travelId = null,
        public readonly ?int $typeId = null,
        public readonly ?bool $active = null,
        public readonly ?string $comment = null,
        public readonly ?string $sortBy = 'id',
        public readonly ?string $sortDirection = 'DESC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            userId: isset($data['userId']) ? (int)$data['userId'] : null,
            travelId: isset($data['travelId']) ? (int)$data['travelId'] : null,
            typeId: isset($data['typeId']) ? (int)$data['typeId'] : null,
            active: isset($data['active']) ? filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : null,
            comment: $data['comment'] ?? null,
            sortBy: $data['sortBy'] ?? 'id',
            sortDirection: strtoupper($data['sortDirection'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        );
    }
}

