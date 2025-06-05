<?php

namespace itaxcix\Core\UseCases\Admission;

use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

interface GetPendingDriversUseCase
{
    public function execute(int $page, int $perPage): PaginationResponseDTO;
}