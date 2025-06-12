<?php

namespace itaxcix\Shared\DTO\useCases\Incident;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta al registrar un incidente")]
readonly class RegisterIncidentResponseDTO
{
    public function __construct(
        #[OA\Property(description: "ID del incidente registrado", example: 123)]
        public int $incidentId,
        #[OA\Property(description: "Mensaje de confirmación", example: "Incidente registrado correctamente.")]
        public string $message
    ) {}
}

