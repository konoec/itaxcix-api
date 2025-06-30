<?php

namespace itaxcix\Shared\DTO\useCases\AuditLog;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AuditLogPaginationResponseDTO",
    description: "DTO de respuesta paginada para registros de auditoría",
    properties: [
        new OA\Property(
            property: "data",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/AuditLogResponseDTO"),
            description: "Lista de registros de auditoría"
        ),
        new OA\Property(
            property: "pagination",
            description: "Información de paginación",
            properties: [
                new OA\Property(property: "current_page", type: "integer", example: 1),
                new OA\Property(property: "per_page", type: "integer", example: 10),
                new OA\Property(property: "total_items", type: "integer", example: 100),
                new OA\Property(property: "total_pages", type: "integer", example: 10)
            ],
            type: "object"
        )
    ],
    type: "object"
)]
readonly class AuditLogPaginationResponseDTO
{
    public function __construct(
        public array $data,
        public int   $currentPage,
        public int   $perPage,
        public int   $totalItems,
        public int   $totalPages
    ) {}

    public function toArray(): array
    {
        return [
            'data' => array_map(fn($item) => $item instanceof AuditLogResponseDTO ? $item->toArray() : $item, $this->data),
            'pagination' => [
                'current_page' => $this->currentPage,
                'per_page' => $this->perPage,
                'total_items' => $this->totalItems,
                'total_pages' => $this->totalPages
            ]
        ];
    }
}
