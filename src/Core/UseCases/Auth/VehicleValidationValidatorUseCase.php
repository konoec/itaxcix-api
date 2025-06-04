<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\VehicleValidationRequestDTO;

interface VehicleValidationValidatorUseCase
{
    public function execute(VehicleValidationRequestDTO $dto): ?array;
}