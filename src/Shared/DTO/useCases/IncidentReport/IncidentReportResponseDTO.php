<?php

namespace itaxcix\Shared\DTO\useCases\IncidentReport;

class IncidentReportResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $userId,
        public readonly ?string $userName,
        public readonly ?int $travelId,
        public readonly ?int $typeId,
        public readonly ?string $typeName,
        public readonly ?string $comment,
        public readonly bool $active
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'travelId' => $this->travelId,
            'typeId' => $this->typeId,
            'typeName' => $this->typeName,
            'comment' => $this->comment,
            'active' => $this->active
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['userId'] ?? null,
            userName: $data['userName'] ?? null,
            travelId: $data['travelId'] ?? null,
            typeId: $data['typeId'] ?? null,
            typeName: $data['typeName'] ?? null,
            comment: $data['comment'] ?? null,
            active: $data['active'] ?? true
        );
    }
}

