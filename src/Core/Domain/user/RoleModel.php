<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;

class RoleModel {
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

    public function toEntity(): RoleEntity
    {
        $roleEntity = new RoleEntity();
        $roleEntity->setId($this->id);
        $roleEntity->setName($this->name);
        $roleEntity->setActive($this->active);
        $roleEntity->setWeb($this->web);

        return $roleEntity;
    }
}