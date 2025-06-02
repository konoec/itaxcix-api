<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\ModelEntity;

class ModelModel {
    private ?int $id;
    private string $name;
    private ?BrandModel $brand = null;
    private bool $active = true;

    /**
     * @param ?int $id
     * @param string $name
     * @param BrandModel|null $brand
     * @param bool $active
     */
    public function __construct(?int $id, string $name, ?BrandModel $brand, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->brand = $brand;
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

    public function getBrand(): ?BrandModel
    {
        return $this->brand;
    }

    public function setBrand(?BrandModel $brand): void
    {
        $this->brand = $brand;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function toEntity(): ModelEntity
    {
        $modelEntity = new ModelEntity();
        $modelEntity->setId($this->id);
        $modelEntity->setName($this->name);
        $modelEntity->setActive($this->active);
        $modelEntity->setBrand($this->brand?->toEntity());
        return $modelEntity;
    }
}