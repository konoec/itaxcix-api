<?php

namespace itaxcix\Core\Handler\Travel;

use itaxcix\Core\UseCases\Travel\RequestNewTravelUseCase;
use itaxcix\Shared\DTO\useCases\Travel\RequestTravelDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class RequestNewTravelUseCaseHandler implements RequestNewTravelUseCase
{

    public function execute(RequestTravelDTO $dto): ?TravelResponseDTO
    {
        // TODO: Implement execute() method.
    }
}