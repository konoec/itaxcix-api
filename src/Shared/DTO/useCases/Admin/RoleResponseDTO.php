<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO de respuesta de rol."
 * )
 */
#[OA\Schema(description: "Datos de respuesta de rol")]
readonly class RoleResponseDTO
{
    public function __construct(
        #[OA\Property(description: "ID del rol", example: 1)]
        public int $id,
        #[OA\Property(description: "Nombre del rol", example: "Administrador")]
        public string $name,
        #[OA\Property(description: "Si el rol está activo", example: true)]
        public bool $active,
        #[OA\Property(description: "Si el rol es para web", example: false)]
        public bool $web
    ) {}
}
