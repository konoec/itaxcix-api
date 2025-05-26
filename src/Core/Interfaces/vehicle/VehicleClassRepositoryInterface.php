<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleClassModel;

interface VehicleClassRepositoryInterface
{
    public function findAllVehicleClassByName(string $name): ?VehicleClassModel;
    public function saveVehicleClass(VehicleClassModel $vehicleClassModel): VehicleClassModel;
}