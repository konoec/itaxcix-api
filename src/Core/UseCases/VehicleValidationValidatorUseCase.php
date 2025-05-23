<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\VehicleValidationRequestDTO;

interface VehicleValidationValidatorUseCase
{
    public function execute(VehicleValidationRequestDTO $dto): ?array;
}