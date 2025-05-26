<?php

namespace itaxcix\Core\Domain\company;

use itaxcix\Infrastructure\Database\Entity\company\CompanyEntity;

class CompanyModel {
    private int $id;
    private string $ruc;
    private ?string $name = null;
    private bool $active = true;

    /**
     * @param int $id
     * @param string $ruc
     * @param string|null $name
     * @param bool $active
     */
    public function __construct(int $id, string $ruc, ?string $name, bool $active)
    {
        $this->id = $id;
        $this->ruc = $ruc;
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

    public function getRuc(): string
    {
        return $this->ruc;
    }

    public function setRuc(string $ruc): void
    {
        $this->ruc = $ruc;
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

    public function toEntity(): CompanyEntity{
        $entity = new CompanyEntity();
        $entity->setId($this->id);
        $entity->setRuc($this->ruc);
        $entity->setName($this->name);
        $entity->setActive($this->active);

        return $entity;
    }

}