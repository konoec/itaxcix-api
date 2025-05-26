<?php

namespace itaxcix\Core\Interfaces\location;
use itaxcix\Core\Domain\location\ProvinceModel;

interface ProvinceRepositoryInterface
{
    public function findProvinceByName(string $name): ?ProvinceModel;
    public function saveProvince(ProvinceModel $provinceModel): ProvinceModel;
}