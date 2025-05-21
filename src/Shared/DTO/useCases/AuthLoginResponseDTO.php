<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class AuthLoginResponseDTO {
    public function __construct(
        public string $token,
        public int $userId,
        public string $username,
        public array $roles,
        public ?bool $availability = null
    ) {}
}