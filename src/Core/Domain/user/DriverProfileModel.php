<?php

namespace itaxcix\Core\Domain\user;

class DriverProfileModel {
    private int $id;
    private ?UserModel $user = null;
    private bool $available = false;
}