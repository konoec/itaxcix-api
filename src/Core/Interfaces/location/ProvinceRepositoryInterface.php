<?php

namespace itaxcix\Core\Interfaces\location;
use itaxcix\Core\Domain\location\ProvinceModel;

interface ProvinceRepositoryInterface
{
    public function findProvinceByName(string $name): ?ProvinceModel;
    public function saveProvince(ProvinceModel $provinceModel): ProvinceModel;

    // Métodos CRUD adicionales
    public function findAll(int $page = 1, int $perPage = 15, array $filters = [], ?string $search = null, string $sortBy = 'name', string $sortOrder = 'ASC'): array;
    public function findById(int $id): ?ProvinceModel;
    public function create(ProvinceModel $provinceModel): ProvinceModel;
    public function update(ProvinceModel $provinceModel): ProvinceModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function existsByUbigeo(string $ubigeo, ?int $excludeId = null): bool;
    public function findByDepartmentId(int $departmentId): array;
    public function countTotal(array $filters = [], ?string $search = null): int;
}