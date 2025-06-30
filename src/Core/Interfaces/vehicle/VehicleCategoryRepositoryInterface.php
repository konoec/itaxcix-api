<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;

interface VehicleCategoryRepositoryInterface
{
    public function findAllVehicleCategoryByName(string $name): ?VehicleCategoryModel;
    public function saveVehicleCategory(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel;

    // Métodos CRUD adicionales
    public function findAll(): array;
    public function findById(int $id): ?VehicleCategoryModel;
    public function create(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel;
    public function update(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function findWithPagination(int $page, int $perPage, array $filters = [], string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function getTotalCount(array $filters = []): int;
}