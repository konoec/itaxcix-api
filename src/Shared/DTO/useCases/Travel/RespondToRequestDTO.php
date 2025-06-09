<?php

namespace itaxcix\Shared\DTO\useCases\Travel;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta a solicitud de viaje")]
readonly class RespondToRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del viaje", example: "12345")]
        public int $travelId,
        #[OA\Property(description: "Enum de estado de respuesta", example: true)]
        public bool $accepted
    ) {}
}