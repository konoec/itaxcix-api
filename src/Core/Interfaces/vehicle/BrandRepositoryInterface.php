<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\BrandModel;

interface BrandRepositoryInterface
{
    public function findAllBrandByName(string $name): ?BrandModel;
    public function saveBrand(BrandModel $brandModel): BrandModel;

    // New CRUD methods
    public function findAll(int $page, int $perPage, ?string $search = null, ?string $name = null, ?bool $active = null, string $sortBy = 'name', string $sortDirection = 'asc', bool $onlyActive = false): array;
    public function findById(int $id): ?BrandModel;
    public function create(BrandModel $brandModel): BrandModel;
    public function update(BrandModel $brandModel): BrandModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function count(?string $search = null, ?string $name = null, ?bool $active = null, bool $onlyActive = false): int;
}