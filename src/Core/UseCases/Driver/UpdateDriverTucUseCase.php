<?php

namespace itaxcix\Core\UseCases\Driver;

use itaxcix\Shared\DTO\useCases\Driver\UpdateTucResponseDto;

interface UpdateDriverTucUseCase
{
    public function execute(int $driverId): UpdateTucResponseDto;
}
