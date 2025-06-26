<?php

namespace itaxcix\Core\Interfaces\company;

use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

interface CompanyRepositoryInterface
{
    public function findCompanyByRuc(string $ruc): ?CompanyModel;
    public function findAllCompanies(): array;
    public function findAllCompaniesPaginated(int $page, int $perPage): PaginationResponseDTO;
    public function findCompanyById(int $id): ?CompanyModel;
    public function saveCompany(CompanyModel $companyModel): CompanyModel;
    public function deleteCompany(int $id): bool;
}