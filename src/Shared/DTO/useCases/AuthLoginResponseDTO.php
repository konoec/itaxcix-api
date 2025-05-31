<?php

namespace itaxcix\Shared\DTO\useCases;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta tras iniciar sesión")]
readonly class AuthLoginResponseDTO {
    public function __construct(
        #[OA\Property(description: "Token JWT generado", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.xxxxx")]
        public string $token,

        #[OA\Property(description: "ID del usuario autenticado", example: 123)]
        public int $userId,

        #[OA\Property(description: "Valor del documento del usuario", example: "12345678")]
        public string $documentValue,

        #[OA\Property(
            property: "roles",
            type: "array",
            items: new OA\Items(type: "string"),
            example: ["admin", "user"]
        )]
        public array $roles,

        #[OA\Property(
            property: "permissions",
            type: "array",
            items: new OA\Items(type: "string"),
            example: ["read_users", "write_users"]
        )]
        public array $permissions
    ) {}
}