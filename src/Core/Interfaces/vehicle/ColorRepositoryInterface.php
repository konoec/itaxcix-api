<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ColorModel;

interface ColorRepositoryInterface
{
    public function findAllColorByName(string $name): ?ColorModel;
    public function saveColor(ColorModel $colorModel): ColorModel;
}