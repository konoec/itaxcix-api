<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para actualizar asignación de permiso a rol."
 * )
 */
#[OA\Schema(description: "Datos para actualizar asignación de permiso a rol")]
readonly class RolePermissionUpdateRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID de la asignación", example: 1)]
        public int $id,
        #[OA\Property(description: "ID del rol", example: 2)]
        public int $roleId,
        #[OA\Property(description: "ID del permiso", example: 3)]
        public int $permissionId,
        #[OA\Property(description: "Si la asignación está activa", example: true)]
        public bool $active = true
    ) {}
}
