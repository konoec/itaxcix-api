<?php

namespace itaxcix\Shared\DTO\useCases\Department;

class DepartmentPaginationRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 15,
        public readonly ?string $search = null,
        public readonly string $orderBy = 'name',
        public readonly string $orderDirection = 'ASC'
    ) {}
}
