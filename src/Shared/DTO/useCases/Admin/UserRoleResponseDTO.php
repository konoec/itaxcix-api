<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta de asignación de usuario a rol")]
readonly class UserRoleResponseDTO
{
    public function __construct(
        #[OA\Property(description: "ID de la asignación", example: 1)]
        public int $id,
        #[OA\Property(description: "ID del usuario", example: 10)]
        public int $userId,
        #[OA\Property(description: "ID del rol", example: 2)]
        public int $roleId,
        #[OA\Property(description: "Si la asignación está activa", example: true)]
        public bool $active
    ) {}
}
