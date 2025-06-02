<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\TucStatusEntity;

class TucStatusModel {
    private ?int $id;
    private string $name;
    private bool $active = true;

    /**
     * @param ?int $id
     * @param string $name
     * @param bool $active
     */
    public function __construct(?int $id, string $name, bool $active)
    {
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

    public function toEntity(): TucStatusEntity
    {
        $entity = new TucStatusEntity();
        $entity->setId($this->id);
        $entity->setName($this->name);
        $entity->setActive($this->active);
        return $entity;
    }
}