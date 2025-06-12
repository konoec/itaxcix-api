<?php

namespace itaxcix\Infrastructure\Database\Entity\incident;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tipo_incidencia')]
class IncidentTypeEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tipo_id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'tipo_nombre', type: 'string', length: 100)]
    private string $name;
    #[ORM\Column(name: 'tipo_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    public function __construct()
    {
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