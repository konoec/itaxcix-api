<?php

namespace itaxcix\Shared\DTO\useCases\Admission;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para aprobar un conductor")]
readonly class ApproveDriverRequestDto
{
    public function __construct(
        #[OA\Property(description: "ID del conductor", example: 123)]
        public int $driverId
    ) {}
}