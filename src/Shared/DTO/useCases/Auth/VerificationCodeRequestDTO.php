<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para verificar el código de recuperación")]
readonly class VerificationCodeRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 123)]
        public int $userId,

        #[OA\Property(description: "Código de verificación", example: "ABC123")]
        public string $code
    ) {}
}