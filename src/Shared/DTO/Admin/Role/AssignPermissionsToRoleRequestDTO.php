<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class AssignPermissionsToRoleRequestDTO
{
    public function __construct(
        public int $roleId,
        public array $permissionIds
    ) {}
}
