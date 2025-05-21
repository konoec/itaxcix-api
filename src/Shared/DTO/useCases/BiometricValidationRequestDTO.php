<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class BiometricValidationRequestDTO {
    public function __construct(
        public int $personId,
        public string $imageBase64,
    ){}
}