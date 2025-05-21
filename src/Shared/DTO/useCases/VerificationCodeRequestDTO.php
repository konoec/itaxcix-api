<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class VerificationCodeRequestDTO {
    public function __construct(
        public int $userId,
        public string $code,
    ) {}
}