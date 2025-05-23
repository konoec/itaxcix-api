<?php

namespace itaxcix\Infrastructure\Database\Entity\infraction;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_gravedad_infraccion')]
class InfractionSeverityEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'grav_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'grav_nombre', type: 'string', length: 100, unique: true)]
    private string $name;
    #[ORM\Column(name: 'grav_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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