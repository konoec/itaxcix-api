<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Infrastructure\Database\Entity\user\PermissionEntity;

class PermissionModel {
    private int $id;
    private string $name;
    private bool $active = true;
    private bool $web = false;

    public function __construct(int $id, string $name, bool $active = true, bool $web = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
        $this->web = $web;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isWeb(): bool
    {
        return $this->web;
    }

    public function setWeb(bool $web): void
    {
        $this->web = $web;
    }

    public function toEntity(): PermissionEntity
    {
        $permissionEntity = new PermissionEntity();
        $permissionEntity->setId($this->id);
        $permissionEntity->setName($this->name);
        $permissionEntity->setActive($this->active);
        $permissionEntity->setWeb($this->web);

        return $permissionEntity;
    }
}