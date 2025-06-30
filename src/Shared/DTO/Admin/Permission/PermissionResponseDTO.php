<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

class PermissionResponseDTO
{
    public readonly int $id;
    public readonly string $name;
    public readonly bool $active;
    public readonly bool $web;

    public function __construct(
        int $id,
        string $name,
        bool $active,
        bool $web
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
        $this->web = $web;
    }
}
