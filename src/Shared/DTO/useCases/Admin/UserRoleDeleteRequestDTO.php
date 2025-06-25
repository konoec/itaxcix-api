<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos para eliminar asignación de usuario a rol")]
readonly class UserRoleDeleteRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID de la asignación", example: 1)]
        public int $id
    ) {}
}
