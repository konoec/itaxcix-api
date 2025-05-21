<?php

namespace itaxcix\Core\Domain\vehicle;

class VehicleCategoryModel {
    private int $id;
    private ?string $name = null;
    protected bool $active = true;
}