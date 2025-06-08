<?php

namespace itaxcix\Core\UseCases\User;

use itaxcix\Shared\DTO\useCases\User\UserProfilePhotoResponseDTO;

interface UserProfilePhotoUseCase
{
    public function execute(int $userId): ?UserProfilePhotoResponseDTO;
}