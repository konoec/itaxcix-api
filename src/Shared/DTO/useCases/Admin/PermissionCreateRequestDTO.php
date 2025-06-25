<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para crear un permiso."
 * )
 */
#[OA\Schema(description: "Datos para crear un permiso")]
readonly class PermissionCreateRequestDTO
{
    public function __construct(
        #[OA\Property(description: "Nombre del permiso", example: "Ver usuarios")]
        public string $name,
        #[OA\Property(description: "Si el permiso está activo", example: true)]
        public bool $active = true,
        #[OA\Property(description: "Si el permiso es para web", example: false)]
        public bool $web = false
    ) {}
}
