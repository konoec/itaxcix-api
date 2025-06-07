<?php

namespace itaxcix\Core\UseCases\Driver;

use itaxcix\Shared\DTO\useCases\Driver\DriverTucStatusResponseDTO;

interface DriverTucStatusUseCase
{
    public function execute(int $driverId): ?DriverTucStatusResponseDTO;
}