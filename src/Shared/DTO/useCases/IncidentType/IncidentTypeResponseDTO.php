<?php

namespace itaxcix\Shared\DTO\useCases\IncidentType;

class IncidentTypeResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $active
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            active: $data['active']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active
        ];
    }
}

