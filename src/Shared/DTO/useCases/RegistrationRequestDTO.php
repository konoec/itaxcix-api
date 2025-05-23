<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class RegistrationRequestDTO {
    public function __construct(
        public string $password,
        public int $contactTypeId,
        public string $contactValue,
        public int $personId,
        public ?int $vehicleId = null
    ){}
}