<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\FuelTypeModel;

interface FuelTypeRepositoryInterface
{
    public function findAllFuelTypeByName(string $name): ?FuelTypeModel;
    public function saveFuelType(FuelTypeModel $fuelTypeModel): FuelTypeModel;
}