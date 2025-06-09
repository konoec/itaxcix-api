<?php

namespace itaxcix\Core\UseCases\Travel;

use itaxcix\Shared\DTO\useCases\Travel\RequestTravelDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

interface RequestNewTravelUseCase
{
    public function execute(RequestTravelDTO $dto): ?TravelResponseDTO;
}