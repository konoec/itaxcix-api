<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO para eliminar un rol."
 * )
 */
#[OA\Schema(description: "Datos para eliminar un rol")]
readonly class RoleDeleteRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del rol", example: 1)]
        public int $id
    ) {}
}
