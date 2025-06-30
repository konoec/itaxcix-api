<?php

namespace itaxcix\Shared\DTO\useCases\Department;

class DepartmentResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $ubigeo
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ubigeo' => $this->ubigeo
        ];
    }
}
