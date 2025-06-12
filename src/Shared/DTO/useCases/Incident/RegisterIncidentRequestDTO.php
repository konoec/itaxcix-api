<?php

namespace itaxcix\Shared\DTO\useCases\Incident;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para registrar un incidente")]
readonly class RegisterIncidentRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del usuario que reporta el incidente", example: 10)]
        public int $userId,
        #[OA\Property(description: "ID del viaje asociado al incidente", example: 20)]
        public int $travelId,
        #[OA\Property(description: "Nombre del tipo de incidencia", example: "ROBO")]
        public string $typeName,
        #[OA\Property(description: "Comentario adicional", example: "Se reporta robo de pertenencias", nullable: true)]
        public ?string $comment = null
    ) {}
}
