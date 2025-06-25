<?php

namespace itaxcix\Shared\DTO\useCases\Profile;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para solicitar el cambio de correo electrónico")]
readonly class ChangeEmailRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 1)]
        public int $userId,
        #[OA\Property(description: "Nuevo correo electrónico", example: "nuevo@ejemplo.com")]
        public string $email
    ) {}
}
