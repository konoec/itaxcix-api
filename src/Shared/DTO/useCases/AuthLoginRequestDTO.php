<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class AuthLoginRequestDTO {
    public function __construct(
        public string $documentValue,
        public string $password,
        public bool $web = false
    ) {}
}