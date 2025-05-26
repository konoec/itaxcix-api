<?php

namespace itaxcix\Core\Interfaces\location;

use itaxcix\Core\Domain\location\DistrictModel;

interface DistrictRepositoryInterface
{
    public function findDistrictByName(string $name): ?DistrictModel;
    public function saveDistrict(DistrictModel $districtModel): DistrictModel;
}