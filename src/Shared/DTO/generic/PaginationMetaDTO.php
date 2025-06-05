<?php

namespace itaxcix\Shared\DTO\generic;

class PaginationMetaDTO
{
    public function __construct(
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage
    ) {}
}