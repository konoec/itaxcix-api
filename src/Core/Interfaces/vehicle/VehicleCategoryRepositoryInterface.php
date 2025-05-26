<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;

interface VehicleCategoryRepositoryInterface
{
    public function findAllVehicleCategoryByName(string $name): ?VehicleCategoryModel;
    public function saveVehicleCategory(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel;
}