<?php

namespace itaxcix\Core\Handler\Travel;

use itaxcix\Core\UseCases\Travel\CancelTravelUseCase;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class CancelTravelUseCaseHandler implements CancelTravelUseCase
{

    public function execute(TravelIdDTO $dto): ?TravelResponseDTO
    {
        // TODO: Implement execute() method.
    }
}