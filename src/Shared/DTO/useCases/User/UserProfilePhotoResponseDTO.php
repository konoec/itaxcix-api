<?php

namespace itaxcix\Shared\DTO\useCases\User;

readonly class UserProfilePhotoResponseDTO
{
    public function __construct(
        public int $userId,
        public string $imageBase64
    ) {}
}