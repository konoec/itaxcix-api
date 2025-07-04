<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleUserModel;

interface VehicleUserRepositoryInterface
{
    public function findVehicleUserByVehicleId(int $vehicleId): ?VehicleUserModel;
    public function findVehicleUserByUserId(int $userId): ?VehicleUserModel;
    public function findActiveVehicleUsersByUserId(int $userId): array;
    public function findActiveVehicleUserByVehicleId(int $vehicleId): ?VehicleUserModel;
    public function findActiveVehicleUserByUserIdAndVehicleId(int $userId, int $vehicleId): ?VehicleUserModel;
    public function saveVehicleUser(VehicleUserModel $vehicleUserModel): VehicleUserModel;
}