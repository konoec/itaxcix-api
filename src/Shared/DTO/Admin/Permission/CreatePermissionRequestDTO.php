<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

class CreatePermissionRequestDTO
{
    public readonly string $name;
    public readonly bool $active;
    public readonly bool $web;
    public readonly string $description;

    public function __construct(
        string $name,
        bool $active = true,
        bool $web = false,
        string $description = ''
    ) {
        $this->name = $name;
        $this->active = $active;
        $this->web = $web;
        $this->description = $description;
    }
}
