<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;

class BrandModel implements \JsonSerializable {
    private ?int $id = null;
    private string $name;
    private bool $active = true;

    /**
     * @param int|null $id
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

    public function toEntity(): BrandEntity
    {
        $entity = new BrandEntity();
        $entity->setId($this->id);
        $entity->setName($this->name);
        $entity->setActive($this->active);
        return $entity;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
        ];
    }
}