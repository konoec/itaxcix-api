<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class RecoveryStartRequestDTO {
    public function __construct(
        public int $contactTypeId,
        public string $contactValue
    ) {}
}