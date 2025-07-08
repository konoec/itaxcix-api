<?php

namespace itaxcix\Shared\DTO\useCases\TravelReport;

class TravelReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?int $citizenId = null,
        public readonly ?int $driverId = null,
        public readonly ?int $statusId = null,
        public readonly ?string $origin = null,
        public readonly ?string $destination = null,
        public readonly string $sortBy = 'creationDate',
        public readonly string $sortDirection = 'DESC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: min(max((int) ($data['perPage'] ?? 20), 1), 100),
            startDate: $data['startDate'] ?? null,
            endDate: $data['endDate'] ?? null,
            citizenId: isset($data['citizenId']) ? (int) $data['citizenId'] : null,
            driverId: isset($data['driverId']) ? (int) $data['driverId'] : null,
            statusId: isset($data['statusId']) ? (int) $data['statusId'] : null,
            origin: $data['origin'] ?? null,
            destination: $data['destination'] ?? null,
            sortBy: $data['sortBy'] ?? 'creationDate',
            sortDirection: strtoupper($data['sortDirection'] ?? 'DESC')
        );
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'perPage' => $this->perPage,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'citizenId' => $this->citizenId,
            'driverId' => $this->driverId,
            'statusId' => $this->statusId,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection
        ];
    }
}
