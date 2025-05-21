<?php

namespace itaxcix\Core\Domain\user;

class UserRoleModel {
    private int $id;
    private ?RoleModel $role = null;
    private ?UserModel $user = null;
    private bool $active = true;
}