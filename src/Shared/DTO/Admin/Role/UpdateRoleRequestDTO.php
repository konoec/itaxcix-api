<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class UpdateRoleRequestDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $active = true,
        public bool $web = false
    ) {}
}
