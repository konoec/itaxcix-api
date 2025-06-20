<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\DriverProfileResponseDTO;

interface GetDriverProfileUseCase
{
    public function execute(int $userId): ?DriverProfileResponseDTO;
}

