<?php

namespace itaxcix\Core\Domain\travel;

class TravelStatusModel {
    private ?int $id;
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

}