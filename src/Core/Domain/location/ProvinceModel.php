<?php

namespace itaxcix\Core\Domain\location;

class ProvinceModel {
    private int $id;
    private ?string $name = null;
    private ?DepartmentModel $department = null;
    private ?string $ubigeo = null;
}