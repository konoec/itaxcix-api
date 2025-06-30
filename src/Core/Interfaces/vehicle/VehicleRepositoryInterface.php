<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleModel;

interface VehicleRepositoryInterface
{
    public function findAllVehicleByPlate(string $plate): ?VehicleModel;
    public function findAllVehicleById(int $id): ?VehicleModel;
    public function findVehicleById(int $id): ?VehicleModel;
    public function saveVehicle(VehicleModel $vehicleModel): VehicleModel;

    // Métodos para reporte administrativo de vehículos
    public function findReport(\itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO $dto): array;
    public function countReport(\itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO $dto): int;
}