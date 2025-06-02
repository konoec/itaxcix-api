<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity;

class UserRoleModel {
    private ?int $id;
    private ?RoleModel $role = null;
    private ?UserModel $user = null;
    private bool $active = true;

    public function __construct(?int $id, ?RoleModel $role, ?UserModel $user, bool $active = true)
    {
        $this->id = $id;
        $this->role = $role;
        $this->user = $user;
        $this->active = $active;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
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

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(?UserModel $user): void
    {
        $this->user = $user;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function toEntity(): UserRoleEntity
    {
        $userRoleEntity = new UserRoleEntity();
        $userRoleEntity->setId($this->id);
        $userRoleEntity->setActive($this->active);
        if ($this->role) {
            $userRoleEntity->setRole($this->role->toEntity());
        }
        if ($this->user) {
            $userRoleEntity->setUser($this->user->toEntity());
        }
        return $userRoleEntity;
    }
}