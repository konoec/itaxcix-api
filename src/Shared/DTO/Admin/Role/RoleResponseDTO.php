<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class RoleResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $active,
        public bool $web
    ) {}
}
