<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\DriverStatusModel;

interface DriverStatusRepositoryInterface {
    public function findDriverStatusByName(string $name): ?DriverStatusModel;
}