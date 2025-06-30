<?php

namespace itaxcix\Shared\DTO\useCases\TravelReport;

class TravelReportResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $citizenName,
        public readonly ?string $driverName,
        public readonly ?string $origin,
        public readonly ?string $destination,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly string $creationDate,
        public readonly string $status
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'citizenName' => $this->citizenName,
            'driverName' => $this->driverName,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'creationDate' => $this->creationDate,
            'status' => $this->status
        ];
    }
}

