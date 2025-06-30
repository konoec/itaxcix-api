<?php

namespace itaxcix\Shared\DTO\useCases\InfractionReport;

class InfractionReportResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $userId,
        public readonly ?string $userName,
        public readonly ?int $severityId,
        public readonly ?string $severityName,
        public readonly ?int $statusId,
        public readonly ?string $statusName,
        public readonly string $date,
        public readonly ?string $description
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'severityId' => $this->severityId,
            'severityName' => $this->severityName,
            'statusId' => $this->statusId,
            'statusName' => $this->statusName,
            'date' => $this->date,
            'description' => $this->description
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['userId'] ?? null,
            userName: $data['userName'] ?? null,
            severityId: $data['severityId'] ?? null,
            severityName: $data['severityName'] ?? null,
            statusId: $data['statusId'] ?? null,
            statusName: $data['statusName'] ?? null,
            date: $data['date'],
            description: $data['description'] ?? null
        );
    }
}

