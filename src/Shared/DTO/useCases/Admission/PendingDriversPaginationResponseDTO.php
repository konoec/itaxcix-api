<?php

namespace itaxcix\Shared\DTO\useCases\Admission;

class PendingDriversPaginationResponseDTO
{
    public function __construct(
        public array $items,                // PendingDriverResponseDTO[]
        public int   $total,
        public int   $perPage,
        public int   $currentPage,
        public int   $lastPage
    ) {}
}