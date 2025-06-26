<?php

namespace itaxcix\Core\UseCases\Company;

use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

interface CompanyCreateUseCase
{
    /**
     * @param CompanyRequestDTO $dto
     * @return array
     */
    public function execute(CompanyRequestDTO $dto): array;
}
