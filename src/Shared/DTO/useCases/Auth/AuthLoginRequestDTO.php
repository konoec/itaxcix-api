<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos para iniciar sesión")]
readonly class AuthLoginRequestDTO {
    public function __construct(
        #[OA\Property(description: "Valor del documento de identidad del usuario", example: "12345678")]
        public string $documentValue,

        #[OA\Property(description: "Contraseña del usuario", example: "Password123@")]
        public string $password,

        #[OA\Property(description: "Indica si el inicio es desde web", example: false)]
        public bool $web = false
    ) {}
}