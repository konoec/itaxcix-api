<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\vehiculo\ModeloRepository;

#[ORM\Entity(repositoryClass: ModeloRepository::class)]
#[ORM\Table(name: 'tb_modelo')]
class Modelo {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'mode_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'mode_nombre', type: 'string', length: 50, nullable: false)]
    private string $nombre;

    #[ORM\ManyToOne(targetEntity: Marca::class)]
    #[ORM\JoinColumn(name: 'mode_marca_id', referencedColumnName: 'marc_id')]
    private ?Marca $marca = null;

    #[ORM\Column(name: 'mode_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getMarca(): ?Marca
    {
        return $this->marca;
    }

    public function setMarca(?Marca $marca): void
    {
        $this->marca = $marca;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }
}