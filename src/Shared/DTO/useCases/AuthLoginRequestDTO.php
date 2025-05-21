<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class AuthLoginRequestDTO {
    public function __construct(
        public string $username,
        public string $password
    ) {}
}