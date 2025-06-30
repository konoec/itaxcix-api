<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

readonly class UpdatePermissionRequestDTO
{
    public int $id;
    public string $name;
    public bool $active;
    public bool $web;

    public function __construct(
        int $id,
        string $name,
        bool $active = true,
        bool $web = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
        $this->web = $web;
    }
}
