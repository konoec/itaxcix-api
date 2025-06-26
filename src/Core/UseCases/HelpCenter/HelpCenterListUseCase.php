<?php

namespace itaxcix\Core\UseCases\HelpCenter;

use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterPaginationRequestDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

interface HelpCenterListUseCase
{
    /**
     * @param HelpCenterPaginationRequestDTO $dto
     * @return PaginationResponseDTO
     */
    public function execute(HelpCenterPaginationRequestDTO $dto): PaginationResponseDTO;
}
