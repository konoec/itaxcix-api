<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Listado de asignaciones de roles a usuarios")]
readonly class UserRoleListResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Lista paginada de asignaciones de roles a usuarios")]
        public PaginationResponseDTO $data
    ) {}
}
