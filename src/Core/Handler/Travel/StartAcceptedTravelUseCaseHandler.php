<?php

namespace itaxcix\Core\Handler\Travel;

use itaxcix\Core\UseCases\Travel\StartAcceptedTravelUseCase;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class StartAcceptedTravelUseCaseHandler implements StartAcceptedTravelUseCase
{

    public function execute(TravelIdDTO $dto): ?TravelResponseDTO
    {
        // TODO: Implement execute() method.
    }
}