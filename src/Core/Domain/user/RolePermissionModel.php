<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Infrastructure\Database\Entity\user\RolePermissionEntity;

class RolePermissionModel {
    private ?int $id;
    private ?RoleModel $role = null;
    private ?PermissionModel $permission = null;
    private bool $active = true;

    public function __construct(?int $id, ?RoleModel $role, ?PermissionModel $permission, bool $active = true)
    {
        $this->id = $id;
        $this->role = $role;
        $this->permission = $permission;
        $this->active = $active;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRole(): ?RoleModel
    {
        return $this->role;
    }

    public function setRole(?RoleModel $role): void
    {
        $this->role = $role;
    }

    public function getPermission(): ?PermissionModel
    {
        return $this->permission;
    }

    public function setPermission(?PermissionModel $permission): void
    {
        $this->permission = $permission;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function toEntity(?RolePermissionEntity $existing = null): RolePermissionEntity
    {
        if ($existing === null) {
            $existing = new RolePermissionEntity();
        }
        $existing->setId($this->id);
        $existing->setActive($this->active);
        if ($this->role !== null) {
            $existing->setRole($this->role->toEntity());
        }
        if ($this->permission !== null) {
            $existing->setPermission($this->permission->toEntity());
        }
        return $existing;
    }
}