<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para re-enviar el código de verificación")]
class ResendVerificationCodeRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del usuario para la que se reenvía el código de verificación", example: 123)]
        public int $userId
    ) {}
}