<?php

namespace itaxcix\Shared\DTO\useCases\Admission;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para aprobar un conductor")]
readonly class PendingDriverResponseDTO
{
    public function __construct(
        #[OA\Property(description: "ID del conductor", example: 1)]
        public int $driverId,
        #[OA\Property(description: "Nombre completo del conductor", example: "Juan Pérez Gómez")]
        public string $fullName,
        #[OA\Property(description: "Documento del conductor", example: "73486545")]
        public string $documentValue,
        #[OA\Property(description: "Valor de la placa que conduce", example: "ABC123")]
        public string $plateValue,
        #[OA\Property(description: "Contacto del conductor", example: "+51987654321")]
        public string $contactValue,
    ) {}
}