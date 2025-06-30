<?php

namespace itaxcix\Shared\DTO\useCases\Vehicle;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos para asociar un usuario con un vehículo")]
readonly class AssociateVehicleRequestDto
{
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 123)]
        public int $userId,
        #[OA\Property(description: "Placa del vehículo", example: "ABC-123")]
        public string $plateValue
    ) {}
}
