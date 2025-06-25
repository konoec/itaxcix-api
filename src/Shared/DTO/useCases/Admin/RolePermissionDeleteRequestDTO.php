<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para eliminar asignación de permiso a rol."
 * )
 */
#[OA\Schema(description: "Datos para eliminar asignación de permiso a rol")]
readonly class RolePermissionDeleteRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID de la asignación", example: 1)]
        public int $id
    ) {}
}
