<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   description="DTO de listado de roles."
 * )
 */
#[OA\Schema(description: "Listado de roles")]
readonly class RoleListResponseDTO
{
    /**
     * @param RoleResponseDTO[] $roles
     */
    public function __construct(
        #[OA\Property(description: "Lista paginada de roles")]
        public PaginationResponseDTO $data
    ) {}
}
