<?php

namespace itaxcix\Shared\DTO\useCases\Emergency;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para guardar el número de emergencia")]
readonly class EmergencyNumberRequestDTO {
    public function __construct(
        #[OA\Property(description: "Número de emergencia", example: "+51999999999")]
        public string $number,
    ) {}
}

