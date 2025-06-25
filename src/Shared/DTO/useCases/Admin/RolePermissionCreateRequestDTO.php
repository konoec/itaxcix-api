<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para crear asignación de permiso a rol."
 * )
 */
#[OA\Schema(description: "Datos para crear asignación de permiso a rol")]
readonly class RolePermissionCreateRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del rol", example: 2)]
        public int $roleId,
        #[OA\Property(description: "ID del permiso", example: 3)]
        public int $permissionId,
        #[OA\Property(description: "Si la asignación está activa", example: true)]
        public bool $active = true
    ) {}
}
