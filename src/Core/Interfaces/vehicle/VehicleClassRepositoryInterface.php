<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleClassModel;

interface VehicleClassRepositoryInterface
{
    public function findAllVehicleClassByName(string $name): ?VehicleClassModel;
    public function saveVehicleClass(VehicleClassModel $vehicleClassModel): VehicleClassModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?VehicleClassModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}