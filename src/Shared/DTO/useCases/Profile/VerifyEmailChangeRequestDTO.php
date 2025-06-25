<?php

namespace itaxcix\Shared\DTO\useCases\Profile;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para verificar el cambio de correo electrónico")]
readonly class VerifyEmailChangeRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 1)]
        public int $userId,
        #[OA\Property(description: "Código de verificación recibido", example: "123456")]
        public string $code
    ) {}
}
