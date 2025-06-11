<?php

namespace itaxcix\Shared\DTO\useCases\Emergency;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta con el número de emergencia configurado")]
readonly class EmergencyNumberResponseDTO {
    public function __construct(
        #[OA\Property(description: "Número de emergencia", example: "+51999999999")]
        public string $number,
    ) {}
}

