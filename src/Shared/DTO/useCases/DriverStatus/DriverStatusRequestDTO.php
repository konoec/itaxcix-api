<?php

namespace itaxcix\Shared\DTO\useCases\DriverStatus;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "DriverStatusRequestDTO",
    description: "DTO de solicitud para crear o actualizar estado de conductor",
    required: ["name"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1, nullable: true, description: "ID del estado de conductor (solo para actualización)"),
        new OA\Property(property: "name", type: "string", example: "Disponible", description: "Nombre del estado del conductor"),
        new OA\Property(property: "active", type: "boolean", example: true, description: "Indica si el estado está activo")
    ],
    type: "object"
)]
class DriverStatusRequestDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly bool $active = true
    ) {}
}
