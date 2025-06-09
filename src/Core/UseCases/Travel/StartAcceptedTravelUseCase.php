<?php

namespace itaxcix\Core\UseCases\Travel;

use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

interface StartAcceptedTravelUseCase
{
    public function execute(TravelIdDTO $dto): ?TravelResponseDTO;
}