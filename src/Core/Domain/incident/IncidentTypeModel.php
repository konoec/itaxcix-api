<?php

namespace itaxcix\Core\Domain\incident;

class IncidentTypeModel {
    private ?int $id = null;
    private string $name;
    private bool $active = true;

    public function __construct(?int $id = null, string $name = '', bool $active = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): void { $this->active = $active; }
}