<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO de respuesta de permiso."
 * )
 */
#[OA\Schema(description: "Datos de respuesta de permiso")]
readonly class PermissionResponseDTO
{
    public function __construct(
        #[OA\Property(description: "ID del permiso", example: 1)]
        public int $id,
        #[OA\Property(description: "Nombre del permiso", example: "Ver usuarios")]
        public string $name,
        #[OA\Property(description: "Si el permiso está activo", example: true)]
        public bool $active,
        #[OA\Property(description: "Si el permiso es para web", example: false)]
        public bool $web
    ) {}
}
