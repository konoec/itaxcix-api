<?php

namespace itaxcix\Core\Domain\vehicle;

class VehicleModel {
    private int $id;
    private string $licensePlate;
    private ?ModelModel $model = null;
    private ?ColorModel $color = null;
    private ?int $manufactureYear = null;
    private ?int $seatCount = null;
    private ?int $passengerCount = null;
    private ?FuelTypeModel $fuelType = null;
    private ?VehicleClassModel $vehicleClass = null;
    private ?VehicleCategoryModel $category = null;
    private bool $active = true;
}