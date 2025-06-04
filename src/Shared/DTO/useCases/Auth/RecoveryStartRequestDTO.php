<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para iniciar la recuperación de contraseña")]
readonly class RecoveryStartRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID del tipo de contacto", example: 2)]
        public int $contactTypeId,

        #[OA\Property(description: "Valor del contacto (correo o teléfono)", example: "+51935123123")]
        public string $contactValue
    ) {}
}