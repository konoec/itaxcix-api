<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class GetRoleWithPermissionsRequestDTO
{
    public function __construct(
        public int $roleId
    ) {}
}
