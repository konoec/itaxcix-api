<?php

namespace itaxcix\Core\Interfaces\user;

interface RolePermissionRepositoryInterface {
    public function findPermissionsByRoleId(int $roleId, bool $web): array;
}