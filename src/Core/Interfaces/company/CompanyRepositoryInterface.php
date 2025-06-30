<?php

namespace itaxcix\Core\Interfaces\company;

use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;

interface CompanyRepositoryInterface
{
    public function findAll(CompanyPaginationRequestDTO $request): array;
    public function findById(int $id): ?CompanyModel;
    public function create(CompanyModel $company): CompanyModel;
    public function update(CompanyModel $company): CompanyModel;
    public function delete(int $id): bool;
    public function existsByRuc(string $ruc, ?int $excludeId = null): bool;
    public function count(CompanyPaginationRequestDTO $request): int;
}
