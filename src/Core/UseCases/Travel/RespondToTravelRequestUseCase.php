<?php

namespace itaxcix\Core\UseCases\Travel;

use itaxcix\Shared\DTO\useCases\Travel\RespondToRequestDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

interface RespondToTravelRequestUseCase
{
    public function execute(RespondToRequestDTO $dto): ?TravelResponseDTO;
}