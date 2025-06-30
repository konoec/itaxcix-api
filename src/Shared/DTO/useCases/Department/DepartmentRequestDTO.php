<?php

namespace itaxcix\Shared\DTO\useCases\Department;

class DepartmentRequestDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $ubigeo
    ) {}
}
