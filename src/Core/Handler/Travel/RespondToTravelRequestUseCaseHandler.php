<?php

namespace itaxcix\Core\Handler\Travel;

use itaxcix\Core\UseCases\Travel\RespondToTravelRequestUseCase;
use itaxcix\Shared\DTO\useCases\Travel\RespondToRequestDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class RespondToTravelRequestUseCaseHandler implements RespondToTravelRequestUseCase
{

    public function execute(RespondToRequestDTO $dto): ?TravelResponseDTO
    {
        // TODO: Implement execute() method.
    }
}