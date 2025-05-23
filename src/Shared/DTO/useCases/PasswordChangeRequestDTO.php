<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class PasswordChangeRequestDTO {
    public function __construct(
        public int $userId,
        public string $newPassword,
        public string $repeatPassword
    ) {}
}