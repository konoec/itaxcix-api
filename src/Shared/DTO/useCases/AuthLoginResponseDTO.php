<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class AuthLoginResponseDTO {
    public function __construct(
        public string $token,
        public int $userId,
        public string $documentValue,
        public array $roles = [],
        public array $permissions = [],
        public ?bool $availability = null
    ) {}
}