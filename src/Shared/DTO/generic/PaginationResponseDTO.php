<?php

namespace itaxcix\Shared\DTO\generic;

class PaginationResponseDTO
{
    public function __construct(
        public array             $items,
        public PaginationMetaDTO $meta
    ) {}
}