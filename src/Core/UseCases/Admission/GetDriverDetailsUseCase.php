<?php

namespace itaxcix\Core\UseCases\Admission;

use itaxcix\Shared\DTO\useCases\Admission\PendingDriverDetailsResponseDTO;

interface GetDriverDetailsUseCase
{
    public function execute(int $driverId): ?PendingDriverDetailsResponseDTO;
}