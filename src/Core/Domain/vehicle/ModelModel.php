<?php

namespace itaxcix\Core\Domain\vehicle;

class ModelModel {
    private int $id;
    private string $name;
    private ?BrandModel $brand = null;
    private bool $active = true;
}