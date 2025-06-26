<?php

namespace itaxcix\Shared\DTO\useCases\Company;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta con los datos de una empresa")]
class CompanyResponseDTO {
    public function __construct(
        #[OA\Property(description: "ID de la empresa", example: 1)]
        public int $id,
        #[OA\Property(description: "RUC de la empresa", example: "20123456789")]
        public string $ruc,
        #[OA\Property(description: "Nombre de la empresa", example: "Empresa de Transportes SAC")]
        public ?string $name,
        #[OA\Property(description: "Si la empresa está activa", example: true)]
        public bool $active,
    ) {}
}
