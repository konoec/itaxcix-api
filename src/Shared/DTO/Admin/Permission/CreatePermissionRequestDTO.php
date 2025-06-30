<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

readonly class CreatePermissionRequestDTO
{
    public string $name;
    public bool $active;
    public bool $web;

    public function __construct(
        string $name,
        bool $active = true,
        bool $web = false
    ) {
        $this->name = $name;
        $this->active = $active;
        $this->web = $web;
    }
}
