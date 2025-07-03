<?php

namespace itaxcix\Shared\DTO\useCases\Driver;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Información detallada de una TUC")]
readonly class TucInfoDto
{
    public function __construct(
        #[OA\Property(description: "ID de la TUC", example: 456)]
        public int $id,
        #[OA\Property(description: "Fecha de trámite", example: "2024-01-15", nullable: true)]
        public ?string $procedureDate = null,
        #[OA\Property(description: "Fecha de emisión", example: "2024-01-20", nullable: true)]
        public ?string $issueDate = null,
        #[OA\Property(description: "Fecha de vencimiento", example: "2025-01-20", nullable: true)]
        public ?string $expirationDate = null,
        #[OA\Property(description: "Estado de la TUC", example: "VIGENTE", nullable: true)]
        public ?string $status = null,
        #[OA\Property(description: "Tipo de procedimiento", example: "RENOVACIÓN", nullable: true)]
        public ?string $procedureType = null,
        #[OA\Property(description: "Modalidad", example: "PRESENCIAL", nullable: true)]
        public ?string $modality = null,
        #[OA\Property(description: "Empresa", example: "EMPRESA DE TRANSPORTE S.A.", nullable: true)]
        public ?string $company = null,
        #[OA\Property(description: "Distrito", example: "LIMA", nullable: true)]
        public ?string $district = null,
        #[OA\Property(description: "Días hasta el vencimiento", example: 30, nullable: true)]
        public ?int $daysUntilExpiration = null,
        #[OA\Property(description: "Indica si la TUC está vigente", example: true)]
        public bool $isActive = false
    ) {}
}
