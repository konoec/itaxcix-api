<?php

namespace itaxcix\Core\UseCases\Vehicle;

use itaxcix\Shared\DTO\useCases\Vehicle\AssociateVehicleRequestDto;
use itaxcix\Shared\DTO\useCases\Vehicle\AssociateVehicleResponseDto;

interface AssociateUserVehicleUseCase
{
    public function execute(AssociateVehicleRequestDto $dto): AssociateVehicleResponseDto;
}
