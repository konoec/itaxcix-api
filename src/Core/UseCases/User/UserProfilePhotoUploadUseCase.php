<?php

namespace itaxcix\Core\UseCases\User;

use itaxcix\Shared\DTO\useCases\User\UserProfilePhotoUploadRequestDTO;

interface UserProfilePhotoUploadUseCase
{
    public function execute(UserProfilePhotoUploadRequestDTO $request): ?array;
}