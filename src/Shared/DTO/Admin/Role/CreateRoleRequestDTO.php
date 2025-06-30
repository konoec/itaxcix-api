<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class CreateRoleRequestDTO
{
    public function __construct(
        public string $name,
        public ?bool $active = true,
        public ?bool $web = false
    ) {}
}
