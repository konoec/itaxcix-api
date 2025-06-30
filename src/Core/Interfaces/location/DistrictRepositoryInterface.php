<?php

namespace itaxcix\Core\Interfaces\location;

use itaxcix\Core\Domain\location\DistrictModel;

interface DistrictRepositoryInterface
{
    public function findDistrictByName(string $name): ?DistrictModel;
    public function saveDistrict(DistrictModel $districtModel): DistrictModel;

    public function findAll(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?int $provinceId = null,
        ?string $ubigeo = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc'
    ): array;

    public function findById(int $id): ?DistrictModel;

    public function create(DistrictModel $district): DistrictModel;

    public function update(DistrictModel $district): DistrictModel;

    public function delete(int $id): bool;

    public function existsByName(string $name, ?int $excludeId = null): bool;

    public function existsByUbigeo(string $ubigeo, ?int $excludeId = null): bool;

    public function countByProvince(int $provinceId): int;
}