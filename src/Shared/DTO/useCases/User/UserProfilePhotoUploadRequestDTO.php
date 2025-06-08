<?php

namespace itaxcix\Shared\DTO\useCases\User;

readonly class UserProfilePhotoUploadRequestDTO
{
    public function __construct(
        public int $userId,
        public string $imageBase64
    ) {}
}