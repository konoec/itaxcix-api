<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Listado de permisos")]
readonly class PermissionListResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Lista paginada de permisos")]
        public PaginationResponseDTO $data
    ) {}
}
