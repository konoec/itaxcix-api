<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class DeleteRoleRequestDTO
{
    public function __construct(
        public int $id
    ) {}
}
