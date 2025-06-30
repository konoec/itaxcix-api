<?php

namespace itaxcix\Shared\DTO\useCases\Rating;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Comentario individual recibido por un usuario")]
readonly class UserRatingCommentDto
{
    public function __construct(
        #[OA\Property(description: "ID de la calificación", example: 1)]
        public int $id,
        #[OA\Property(description: "ID del viaje", example: 123)]
        public int $travelId,
        #[OA\Property(description: "Nombre de quien califica", example: "Juan Pérez")]
        public string $raterName,
        #[OA\Property(description: "Puntaje otorgado (1-5)", example: 5)]
        public int $score,
        #[OA\Property(description: "Comentario recibido", example: "Excelente conductor", nullable: true)]
        public ?string $comment,
        #[OA\Property(description: "Fecha del comentario", example: "2025-06-19T10:00:00Z")]
        public string $createdAt
    ) {}
}
