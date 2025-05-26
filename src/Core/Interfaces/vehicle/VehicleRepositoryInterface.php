<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleModel;

interface VehicleRepositoryInterface
{
    public function findAllVehicleByPlate(string $plate): ?VehicleModel;
    public function saveVehicle(VehicleModel $vehicleModel): VehicleModel;
}