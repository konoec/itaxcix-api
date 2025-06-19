<?php

namespace itaxcix\Shared\DTO\useCases\Travel;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para calificar un viaje")]
readonly class RateTravelRequestDto
{
    public function __construct(
        #[OA\Property(description: "ID del viaje", example: 123)]
        public int $travelId,
        #[OA\Property(description: "ID del usuario que califica", example: 456)]
        public int $raterId,
        #[OA\Property(description: "Puntaje otorgado (1-5)", example: 5)]
        public int $score,
        #[OA\Property(description: "Comentario opcional", example: "Buen viaje", nullable: true)]
        public ?string $comment = null
    ) {}
}
