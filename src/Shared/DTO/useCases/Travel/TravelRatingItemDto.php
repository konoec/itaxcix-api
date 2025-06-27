<?php

namespace itaxcix\Shared\DTO\useCases\Travel;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Calificación de un viaje")]
readonly class TravelRatingItemDto
{
    public function __construct(
        #[OA\Property(description: "ID de la calificación", example: 1)]
        public int $id,
        #[OA\Property(description: "ID del viaje", example: 123)]
        public int $travelId,
        #[OA\Property(description: "Nombre del usuario que califica", example: "Juan Pérez")]
        public string $raterName,
        #[OA\Property(description: "Nombre del usuario calificado", example: "María García")]
        public string $ratedName,
        #[OA\Property(description: "Puntaje otorgado (1-5)", example: 5)]
        public int $score,
        #[OA\Property(description: "Comentario", example: "Buen viaje", nullable: true)]
        public ?string $comment,
        #[OA\Property(description: "Fecha de creación", example: "2025-06-19T10:00:00Z")]
        public string $createdAt
    ) {}
}
