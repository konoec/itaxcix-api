<?php

namespace itaxcix\Infrastructure\Database\Entity\company;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_empresa')]
class CompanyEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'empr_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'empr_ruc', type: 'string', length: 11, unique: true, nullable: false)]
    private string $ruc;
    #[ORM\Column(name: 'empr_nombre', type: 'string', length: 100, nullable: true)]
    private ?string $name = null;
    #[ORM\Column(name: 'empr_activo', type: 'boolean', nullable: false, options: ['default' => true])]
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

    public function getRuc(): ?string
    {
        return $this->ruc;
    }

    public function setRuc(?string $ruc): void
    {
        $this->ruc = $ruc;
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