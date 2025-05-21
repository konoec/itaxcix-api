<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Core\Domain\user\UserModel;

class VehicleUserModel {
    private int $id;
    private ?UserModel $user = null;
    private ?VehicleModel $vehicle = null;
    private bool $active = true;
}