<?php

namespace itaxcix\Shared\DTO\useCases\InfractionReport;

class InfractionReportPaginationResponseDTO
{
    public function __construct(
        public readonly array $data,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly int $totalItems,
        public readonly int $totalPages
    ) {}

    public function toArray(): array
    {
        return [
            'data' => array_map(fn($item) => $item instanceof InfractionReportResponseDTO ? $item->toArray() : $item, $this->data),
            'pagination' => [
                'current_page' => $this->currentPage,
                'per_page' => $this->perPage,
                'total_items' => $this->totalItems,
                'total_pages' => $this->totalPages
            ]
        ];
    }
}

