<?php

namespace itaxcix\Shared\DTO\useCases\Travel;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Calificaciones del viaje (conductor y ciudadano)")]
readonly class TravelRatingsByTravelResponseDto
{
    public function __construct(
        #[OA\Property(description: "Calificación dada por el conductor", ref: TravelRatingItemDto::class, nullable: true)]
        public ?TravelRatingItemDto $driverRating,
        #[OA\Property(description: "Calificación dada por el ciudadano", ref: TravelRatingItemDto::class, nullable: true)]
        public ?TravelRatingItemDto $citizenRating
    ) {}
}

