<?php

namespace itaxcix\Core\UseCases\Company;

interface CompanyDeleteUseCase
{
    /**
     * @param int $id
     * @return array
     */
    public function execute(int $id): array;
}
