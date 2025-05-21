<?php

namespace itaxcix\Core\Domain\user;

class RolePermissionModel {
    private int $id;
    private ?RoleModel $role = null;
    private ?PermissionModel $permission = null;
    private bool $active = true;
}