<?php

namespace itaxcix\Shared\DTO\useCases\Admission;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para aprobar un conductor")]
readonly class PendingDriverDetailsResponseDTO
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
        #[OA\Property(description: "Ruc de la empresa a la que forma parte.", example: "20456789012")]
        public string $rucCompany,
        #[OA\Property(description: "Tipo de tramite de tarjeta unica de circulación del conductor", example: "Actualización de datos")]
        public string $tucType,
        #[OA\Property(description: "Modalidad de la tarjeta única de circulación del conductor", example: "Omnibus")]
        public string $tucModality,
        #[OA\Property(description: "Fecha de emisión de la tarjeta única de circulación del conductor", example: "2023-01-15")]
        public string $tucIssueDate,
        #[OA\Property(description: "Fecha de expiración de la tarjeta única de circulación del conductor", example: "2025-01-15")]
        public string $tucExpirationDate,
        #[OA\Property(description: "Estado de la tarjeta única de circulación del conductor", example: "Activo")]
        public string $tucStatus,
    ) {}
}