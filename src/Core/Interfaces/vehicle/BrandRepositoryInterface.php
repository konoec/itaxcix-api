<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\BrandModel;

interface BrandRepositoryInterface
{
    public function findAllBrandByName(string $name): ?BrandModel;
    public function saveBrand(BrandModel $brandModel): BrandModel;
}