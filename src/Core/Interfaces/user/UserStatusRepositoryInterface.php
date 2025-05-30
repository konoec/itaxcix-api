<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserStatusModel;

interface UserStatusRepositoryInterface {
    public function findUserStatusByName(string $name): ?UserStatusModel;
}