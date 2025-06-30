<?php

namespace itaxcix\Shared\DTO\useCases\IncidentType;

class IncidentTypeRequestDTO
{
    public function __construct(
        public readonly string $name,
        public readonly bool $active = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            active: $data['active'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'active' => $this->active
        ];
    }
}

