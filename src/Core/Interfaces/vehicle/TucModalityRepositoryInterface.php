<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucModalityModel;

interface TucModalityRepositoryInterface
{
    public function findAllTucModalityByName(string $name): ?TucModalityModel;
    public function saveTucModality(TucModalityModel $brandModel): TucModalityModel;
}