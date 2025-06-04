<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleUserModel;

interface VehicleUserRepositoryInterface
{
    public function findVehicleUserByVehicleId(int $vehicleId): ?VehicleUserModel;
    public function findVehicleUserByUserId(int $userId): ?VehicleUserModel;
    public function saveVehicleUser(VehicleUserModel $vehicleUserModel): VehicleUserModel;
}