<?php

namespace itaxcix\Core\UseCases\Driver;

use itaxcix\Shared\DTO\useCases\Driver\DriverStatusResponseDTO;

interface ToggleDriverStatusUseCase
{
    public function execute(int $driverId): ?DriverStatusResponseDTO;
}