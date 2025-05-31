<?php

namespace itaxcix\Shared\DTO\useCases;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para cambiar la contraseña")]
readonly class PasswordChangeRequestDTO {
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 123)]
        public int $userId,

        #[OA\Property(description: "Nueva contraseña", example: "nueva@Contrasena123")]
        public string $newPassword,

        #[OA\Property(description: "Confirmación de nueva contraseña", example: "nueva@Contrasena123")]
        public string $repeatPassword
    ) {}
}