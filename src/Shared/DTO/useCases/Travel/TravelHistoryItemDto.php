<?php

namespace itaxcix\Shared\DTO\useCases\Travel;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Elemento del historial de viajes")]
readonly class TravelHistoryItemDto
{
    public function __construct(
        #[OA\Property(description: "ID del viaje", example: 123)]
        public int $id,
        #[OA\Property(description: "Fecha de inicio", example: "2025-06-19T10:00:00Z")]
        public string $startDate,
        #[OA\Property(description: "Origen", example: "Avenida Siempre Viva 123")]
        public string $origin,
        #[OA\Property(description: "Destino", example: "Calle Falsa 456")]
        public string $destination,
        #[OA\Property(description: "Estado del viaje", example: "finalizado")]
        public string $status
    ) {}
}

