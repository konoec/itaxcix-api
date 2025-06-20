<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\AdminProfileResponseDTO;

interface GetAdminProfileUseCase
{
    public function execute(int $userId): ?AdminProfileResponseDTO;
}

