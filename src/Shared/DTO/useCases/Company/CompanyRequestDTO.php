<?php

namespace itaxcix\Shared\DTO\useCases\Company;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para crear o actualizar una empresa")]
readonly class CompanyRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID de la empresa (opcional para crear)", example: 1)]
        public ?int $id,
        #[OA\Property(description: "RUC de la empresa", example: "20123456789")]
        public string $ruc,
        #[OA\Property(description: "Nombre de la empresa", example: "Empresa de Transportes SAC")]
        public ?string $name,
        #[OA\Property(description: "Si la empresa está activa", example: true)]
        public bool $active = true,
    ) {}
}
