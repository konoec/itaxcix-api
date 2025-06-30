<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

class UpdatePermissionRequestDTO
{
    public readonly int $id;
    public readonly string $name;
    public readonly bool $active;
    public readonly bool $web;
    public readonly string $description;

    public function __construct(
        int $id,
        string $name,
        bool $active = true,
        bool $web = false,
        string $description = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
        $this->web = $web;
        $this->description = $description;
    }
}
