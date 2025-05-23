<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_modelo')]
class ModelEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'mode_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'mode_nombre', type: 'string', length: 50, nullable: false)]
    private string $name;
    #[ORM\ManyToOne(targetEntity: BrandEntity::class)]
    #[ORM\JoinColumn(name: 'mode_marca_id', referencedColumnName: 'marc_id')]
    private ?BrandEntity $brand = null;
    #[ORM\Column(name: 'mode_activo', type: 'boolean', nullable: false, options: ['default' => true])]
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

    public function getBrand(): ?BrandEntity
    {
        return $this->brand;
    }

    public function setBrand(?BrandEntity $brand): void
    {
        $this->brand = $brand;
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