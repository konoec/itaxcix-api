<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\FuelTypeModel;

interface FuelTypeRepositoryInterface
{
    public function findAllFuelTypeByName(string $name): ?FuelTypeModel;
    public function saveFuelType(FuelTypeModel $fuelTypeModel): FuelTypeModel;

    // Métodos CRUD adicionales
    public function findAll(): array;
    public function findById(int $id): ?FuelTypeModel;
    public function create(FuelTypeModel $fuelTypeModel): FuelTypeModel;
    public function update(FuelTypeModel $fuelTypeModel): FuelTypeModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function findWithPagination(int $page, int $perPage, array $filters = [], string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function getTotalCount(array $filters = []): int;
}