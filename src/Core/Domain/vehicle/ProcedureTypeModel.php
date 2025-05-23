<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\ProcedureTypeEntity;

class ProcedureTypeModel {
    private int $id;
    private ?string $name = null;
    private bool $active = true;

    /**
     * @param int $id
     * @param string|null $name
     * @param bool $active
     */
    public function __construct(int $id, ?string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
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

    public function toEntity(): ProcedureTypeEntity
    {
        $entity = new ProcedureTypeEntity();
        $entity->setId($this->id);
        $entity->setName($this->name);
        $entity->setActive($this->active);

        return $entity;
    }
}