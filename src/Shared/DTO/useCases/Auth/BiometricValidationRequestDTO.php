<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para validar una imagen biométrica")]
readonly class BiometricValidationRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID único de la persona", example: 12345)]
        public int $personId,

        #[OA\Property(description: "Imagen biométrica en formato Base64", example: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...")]
        public string $imageBase64,
    ) {}
}