<?php

namespace itaxcix\Shared\DTO\useCases\Driver;

readonly class DriverStatusResponseDTO
{
    public function __construct(
        public int $driverId,
        public bool $available
    ) {}
}