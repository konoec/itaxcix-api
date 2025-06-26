<?php

namespace itaxcix\Core\UseCases\Company;

use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

interface CompanyListUseCase
{
    /**
     * @param CompanyPaginationRequestDTO $dto
     * @return PaginationResponseDTO
     */
    public function execute(CompanyPaginationRequestDTO $dto): PaginationResponseDTO;
}
