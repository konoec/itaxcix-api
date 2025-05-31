<?php

namespace itaxcix\Shared\DTO\useCases;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para registrar un nuevo usuario")]
readonly class RegistrationRequestDTO {
    public function __construct(
        #[OA\Property(description: "Contraseña del usuario", example: "contrasena@123")]
        public string $password,

        #[OA\Property(description: "ID del tipo de contacto", example: 1)]
        public int $contactTypeId,

        #[OA\Property(description: "Valor del contacto (correo o teléfono)", example: "usuario@example.com")]
        public string $contactValue,

        #[OA\Property(description: "ID de la persona asociada", example: 123)]
        public int $personId,

        #[OA\Property(description: "ID opcional del vehículo asociado", example: 456)]
        public ?int $vehicleId = null
    ) {}
}