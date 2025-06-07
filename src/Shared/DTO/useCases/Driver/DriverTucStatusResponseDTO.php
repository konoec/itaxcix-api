<?php

namespace itaxcix\Shared\DTO\useCases\Driver;

readonly class DriverTucStatusResponseDTO
{
    public function __construct(
        public int $driverId,
        public bool $hasActiveTuc
    ) {}
}