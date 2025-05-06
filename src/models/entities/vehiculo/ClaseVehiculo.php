<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\vehiculo\ClaseVehiculoRepository;

#[ORM\Entity(repositoryClass: ClaseVehiculoRepository::class)]
#[ORM\Table(name: 'tb_clase_vehiculo')]
class ClaseVehiculo {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'clas_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'clas_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'clas_activo', type: 'boolean', options: ['default' => true])]
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

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }
}