<?php

namespace itaxcix\Core\Interfaces\company;

use itaxcix\Core\Domain\company\CompanyModel;

interface CompanyRepositoryInterface
{
    public function findCompanyByRuc(string $ruc): ?CompanyModel;
    public function saveCompany(CompanyModel $companyModel): CompanyModel;
}