<?php

namespace itaxcix\Shared\DTO\useCases\Company;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Parámetros de paginación para empresas")]
readonly class CompanyPaginationRequestDTO {
    public function __construct(
        #[OA\Property(description: "Página actual", example: 1)]
        public int $page = 1,
        #[OA\Property(description: "Elementos por página", example: 10)]
        public int $perPage = 10,
    ) {}
}
