<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\CitizenProfileResponseDTO;

interface GetCitizenProfileUseCase
{
    public function execute(int $userId): ?CitizenProfileResponseDTO;
}

