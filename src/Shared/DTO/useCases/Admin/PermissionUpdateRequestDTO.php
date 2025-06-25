<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para actualizar un permiso."
 * )
 */
#[OA\Schema(description: "Datos para actualizar un permiso")]
readonly class PermissionUpdateRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del permiso", example: 1)]
        public int $id,
        #[OA\Property(description: "Nombre del permiso", example: "Ver usuarios")]
        public string $name,
        #[OA\Property(description: "Si el permiso está activo", example: true)]
        public bool $active = true,
        #[OA\Property(description: "Si el permiso es para web", example: false)]
        public bool $web = false
    ) {}
}
