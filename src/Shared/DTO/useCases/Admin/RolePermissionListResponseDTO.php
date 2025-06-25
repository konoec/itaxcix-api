<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO de listado de asignaciones de permisos a roles."
 * )
 */
#[OA\Schema(description: "Listado de asignaciones de permisos a roles")]
readonly class RolePermissionListResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Lista paginada de asignaciones de permisos a roles")]
        public PaginationResponseDTO $data
    ) {}
}
