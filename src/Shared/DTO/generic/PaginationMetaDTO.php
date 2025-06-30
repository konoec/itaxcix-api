<?php

namespace itaxcix\Shared\DTO\generic;

class PaginationMetaDTO
{
    public function __construct(
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,

        // Campos adicionales para funcionalidades avanzadas
        public ?string $search = null,
        public ?array $filters = null,
        public ?string $sortBy = null,
        public ?string $sortDirection = null
    ) {}
}