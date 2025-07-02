<?php

namespace itaxcix\Shared\DTO\generic;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaginationInfo",
    description: "Información de paginación",
    type: "object"
)]
class PaginationInfo
{
    #[OA\Property(
        property: "current_page",
        type: "integer",
        description: "Página actual",
        example: 1
    )]
    public int $currentPage;

    #[OA\Property(
        property: "per_page",
        type: "integer",
        description: "Elementos por página",
        example: 10
    )]
    public int $perPage;

    #[OA\Property(
        property: "total_items",
        type: "integer",
        description: "Total de elementos",
        example: 100
    )]
    public int $totalItems;

    #[OA\Property(
        property: "total_pages",
        type: "integer",
        description: "Total de páginas",
        example: 10
    )]
    public int $totalPages;

    public function __construct(
        int $currentPage,
        int $perPage,
        int $totalItems,
        int $totalPages
    ) {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->totalItems = $totalItems;
        $this->totalPages = $totalPages;
    }

    public function toArray(): array
    {
        return [
            'current_page' => $this->currentPage,
            'per_page' => $this->perPage,
            'total_items' => $this->totalItems,
            'total_pages' => $this->totalPages
        ];
    }
}
