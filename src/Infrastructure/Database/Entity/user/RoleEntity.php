<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol')]
class RoleEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rol_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'rol_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'rol_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
    #[ORM\Column(name: 'rol_web', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $web = false;

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

    public function isWeb(): bool
    {
        return $this->web;
    }

    public function setWeb(bool $web): void
    {
        $this->web = $web;
    }
}