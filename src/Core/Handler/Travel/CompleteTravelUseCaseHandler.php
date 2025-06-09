<?php

namespace itaxcix\Core\Handler\Travel;

use itaxcix\Core\UseCases\Travel\CompleteTravelUseCase;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class CompleteTravelUseCaseHandler implements CompleteTravelUseCase
{

    public function execute(TravelIdDTO $dto): ?TravelResponseDTO
    {
        // TODO: Implement execute() method.
    }
}