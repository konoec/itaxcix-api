<?php

namespace itaxcix\Shared\DTO\useCases\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta tras iniciar sesión")]
readonly class AuthLoginResponseDTO {
    public function __construct(
        #[OA\Property(description: "Token JWT generado", example: "eyJhb...")]
        public string $token,

        #[OA\Property(description: "ID del usuario autenticado", example: 123)]
        public int $userId,

        #[OA\Property(description: "Valor del documento del usuario", example: "12345678")]
        public string $documentValue,

        #[OA\Property(description: "Nombre del usuario", example: "Juan")]
        public string $firstName,

        #[OA\Property(description: "Apellido del usuario", example: "Pérez")]
        public string $lastName,

        #[OA\Property(
            property: "roles",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "admin")
                ],
                type: "object"
            ),
            example: [["id" => 1, "name" => "admin"], ["id" => 2, "name" => "user"]]
        )]
        public array $roles,

        #[OA\Property(
            property: "permissions",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "read_users")
                ],
                type: "object"
            ),
            example: [["id" => 1, "name" => "read_users"], ["id" => 2, "name" => "write_users"]]
        )]
        public array $permissions,
        #[OA\Property(description: "Valor de la calificación del usuario", example: 4.5)]
        public ?float $rating
    ) {}
}