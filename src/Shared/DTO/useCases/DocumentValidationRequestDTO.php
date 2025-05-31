<?php

namespace itaxcix\Shared\DTO\useCases;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para validar un documento")]
readonly class DocumentValidationRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID del tipo de documento", example: 1)]
        public int $documentTypeId,

        #[OA\Property(description: "Valor del documento", example: "12345670")]
        public string $documentValue,
    ) {}
}