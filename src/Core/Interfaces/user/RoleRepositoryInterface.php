<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\RoleModel;

interface RoleRepositoryInterface {
    public function findRoleByName(string $name): ?RoleModel;
}