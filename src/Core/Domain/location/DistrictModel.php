<?php

namespace itaxcix\Core\Domain\location;

class DistrictModel {
    private int $id;
    private ?string $name = null;
    private ?ProvinceModel $province = null;
    private ?string $ubigeo = null;
}