<?php

namespace itaxcix\Infrastructure\Database\Entity\configuration;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_configuracion')]
class ConfigurationEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'conf_id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'conf_clave', type: 'string', length: 50)]
    private string $key;
    #[ORM\Column(name: 'conf_valor', type: 'string', length: 255)]
    private string $value;
    #[ORM\Column(name: 'conf_activo', type: 'boolean', options: ['default' => true])]
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