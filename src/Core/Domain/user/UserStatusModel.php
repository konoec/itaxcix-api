<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Infrastructure\Database\Entity\user\UserStatusEntity;

class UserStatusModel {
    private ?int $id = null;
    private string $name;
    private bool $active = true;

    public function __construct(?int $id, string $name, bool $active = true) {
        $this->id = $id;
        $this->name = $name;
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

    public function toEntity(): UserStatusEntity {
        $entity = new UserStatusEntity();
        if ($this->id !== null) {
            $entity->setId($this->id);
        }
        $entity->setName($this->name);
        $entity->setActive($this->active);

        return $entity;
    }
}