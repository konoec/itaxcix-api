<?php

namespace itaxcix\Shared\DTO\useCases\Admission;

readonly class RejectDriverRequestDto
{
    public function __construct(
        public int $driverId
    ) {}
}