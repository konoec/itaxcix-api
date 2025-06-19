<?php

namespace itaxcix\Core\UseCases\Travel;

use itaxcix\Shared\DTO\useCases\Travel\TravelRatingsByTravelResponseDto;

interface GetTravelRatingsByTravelUseCase
{
    public function execute(int $travelId): TravelRatingsByTravelResponseDto;
}

