<?php

namespace itaxcix\Shared\DTO\useCases\DriverStatus;

class DriverStatusPaginationRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $search = null,
        public readonly ?string $name = null,
        public readonly ?bool $active = null,
        public readonly string $sortBy = 'name',
        public readonly string $sortDirection = 'asc',
        public readonly bool $onlyActive = false
    ) {}

    public function isValidSortBy(): bool
    {
        return in_array($this->sortBy, ['id', 'name', 'active']);
    }

    public function isValidSortDirection(): bool
    {
        return in_array(strtolower($this->sortDirection), ['asc', 'desc']);
    }
}
