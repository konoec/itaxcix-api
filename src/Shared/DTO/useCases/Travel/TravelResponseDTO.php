<?php

namespace itaxcix\Shared\DTO\useCases\Travel;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta tras iniciar sesión")]
readonly class TravelResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Mensaje de respuesta", example: "Viaje cancelado correctamente")]
        public string $message,
        #[OA\Property(description: "ID del viaje", example: 12)]
        public int $travelId,
    ){}
}