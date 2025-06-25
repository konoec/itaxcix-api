<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para eliminar un permiso."
 * )
 */
#[OA\Schema(description: "Datos para eliminar un permiso")]
readonly class PermissionDeleteRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del permiso", example: 1)]
        public int $id
    ) {}
}
