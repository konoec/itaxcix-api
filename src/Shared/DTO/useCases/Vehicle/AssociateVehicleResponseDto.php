<?php

namespace itaxcix\Shared\DTO\useCases\Vehicle;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta de la asociación de vehículo")]
readonly class AssociateVehicleResponseDto
{
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 123)]
        public int $userId,
        #[OA\Property(description: "ID del vehículo asociado", example: 456)]
        public int $vehicleId,
        #[OA\Property(description: "Placa del vehículo", example: "ABC-123")]
        public string $plateValue,
        #[OA\Property(description: "Indica si se creó un nuevo vehículo", example: false)]
        public bool $vehicleCreated,
        #[OA\Property(description: "Número de TUCs actualizadas", example: 2)]
        public int $tucsUpdated,
        #[OA\Property(description: "Mensaje descriptivo", example: "Vehículo asociado exitosamente")]
        public string $message
    ) {}
}
