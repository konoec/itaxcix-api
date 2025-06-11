<?php

namespace itaxcix\Core\Domain\configuration;

class ConfigurationModel {
    private ?int $id = null;
    private string $key;
    private string $value;
    private bool $active = true;

    public function __construct(?int $id = null, string $key = '', string $value = '', bool $active = true)
    {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
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

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}