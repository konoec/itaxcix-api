<?php

namespace itaxcix\Shared\DTO\useCases\Driver;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Información de una TUC actualizada")]
readonly class TucUpdateDto
{
    public function __construct(
        #[OA\Property(description: "Placa del vehículo", example: "ABC-123")]
        public string $plate,
        #[OA\Property(description: "Fecha de expedición anterior", example: "2024-01-15", nullable: true)]
        public ?string $previousExpirationDate,
        #[OA\Property(description: "Nueva fecha de expedición", example: "2025-01-15")]
        public string $newExpirationDate,
        #[OA\Property(description: "Estado de la TUC", example: "VIGENTE")]
        public string $status
    ) {}
}
