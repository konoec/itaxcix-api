<?php

namespace itaxcix\Shared\DTO\useCases\TravelReport;

class TravelReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $startDate = null, // formato: Y-m-d
        public readonly ?string $endDate = null,   // formato: Y-m-d
        public readonly ?int $citizenId = null,
        public readonly ?int $driverId = null,
        public readonly ?int $statusId = null,
        public readonly ?string $origin = null,
        public readonly ?string $destination = null,
        public readonly ?string $sortBy = 'creationDate',
        public readonly ?string $sortDirection = 'DESC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            startDate: $data['startDate'] ?? null,
            endDate: $data['endDate'] ?? null,
            citizenId: isset($data['citizenId']) ? (int)$data['citizenId'] : null,
            driverId: isset($data['driverId']) ? (int)$data['driverId'] : null,
            statusId: isset($data['statusId']) ? (int)$data['statusId'] : null,
            origin: $data['origin'] ?? null,
            destination: $data['destination'] ?? null,
            sortBy: $data['sortBy'] ?? 'creationDate',
            sortDirection: strtoupper($data['sortDirection'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        );
    }
}

