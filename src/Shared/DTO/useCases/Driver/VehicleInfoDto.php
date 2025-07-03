<?php

namespace itaxcix\Shared\DTO\useCases\Driver;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Información del vehículo del conductor")]
readonly class VehicleInfoDto
{
    public function __construct(
        #[OA\Property(description: "ID del vehículo", example: 123)]
        public int $id,
        #[OA\Property(description: "Placa del vehículo", example: "ABC-123")]
        public string $licensePlate,
        #[OA\Property(description: "Año de fabricación", example: 2020, nullable: true)]
        public ?int $manufactureYear = null,
        #[OA\Property(description: "Número de asientos", example: 5, nullable: true)]
        public ?int $seatCount = null,
        #[OA\Property(description: "Modelo del vehículo", example: "Toyota Corolla", nullable: true)]
        public ?string $model = null,
        #[OA\Property(description: "Color del vehículo", example: "Blanco", nullable: true)]
        public ?string $color = null
    ) {}
}
