<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para validar un vehículo")]
readonly class VehicleValidationRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID del tipo de documento", example: 1)]
        public int $documentTypeId,

        #[OA\Property(description: "Valor del documento", example: "12345678")]
        public string $documentValue,

        #[OA\Property(description: "Número de placa del vehículo", example: "ABC123")]
        public string $plateValue
    ) {}
}