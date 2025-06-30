<?php

namespace itaxcix\Shared\DTO\useCases\Rating;

use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Comentarios recibidos por un usuario con su promedio de calificación (paginado)")]
readonly class UserRatingsResponseDto
{
    public function __construct(
        #[OA\Property(description: "Promedio de calificación del usuario", example: 4.5)]
        public float $averageRating,
        #[OA\Property(description: "Número total de calificaciones recibidas", example: 25)]
        public int $totalRatings,
        #[OA\Property(type: "array", items: new OA\Items(ref: UserRatingCommentDto::class))]
        public array $comments,
        #[OA\Property(ref: PaginationMetaDTO::class)]
        public PaginationMetaDTO $meta
    ) {}
}
