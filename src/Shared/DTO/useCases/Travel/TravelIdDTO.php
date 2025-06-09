<?php

namespace itaxcix\Shared\DTO\useCases\Travel;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos del ID de viaje")]
readonly class TravelIdDTO
{
    public function __construct(
        public int $travelId
    ){}
}