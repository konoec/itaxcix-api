<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_permiso')]
class PermissionEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perm_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'perm_nombre', type: 'string', length: 100, unique: true)]
    private string $name;
    #[ORM\Column(name: 'perm_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
    #[ORM\Column(name: 'perm_web', type: 'boolean', options: ['default' => false])]
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