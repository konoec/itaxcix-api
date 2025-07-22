<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleModel;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO;

interface VehicleRepositoryInterface
{
    public function findAllVehicleByPlate(string $plate): ?VehicleModel;
    public function findAllVehicleById(int $id): ?VehicleModel;
    public function findVehicleById(int $id): ?VehicleModel;
    public function saveVehicle(VehicleModel $vehicleModel): VehicleModel;

    // Métodos para reporte administrativo de vehículos
    public function findReport(VehicleReportRequestDTO $dto): array;
    public function countReport(VehicleReportRequestDTO $dto): int;
    public function findActiveByColorId(int $colorId): array;
    public function findActiveByFuelTypeId(int $fuelTypeId): array;
}