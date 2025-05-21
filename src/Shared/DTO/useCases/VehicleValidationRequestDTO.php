<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class VehicleValidationRequestDTO {
    public function __construct(
        public int $documentTypeId,
        public string $documentValue,
        public string $plateValue,
    ){}
}