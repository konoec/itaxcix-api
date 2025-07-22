<?php

namespace itaxcix\Core\Interfaces\location;

use itaxcix\Core\Domain\location\CoordinatesModel;

interface CoordinatesRepositoryInterface
{
    public function saveCoordinates(CoordinatesModel $coordinatesModel): CoordinatesModel;
    public function findByDistrictId(int $districtId): ?CoordinatesModel;
}