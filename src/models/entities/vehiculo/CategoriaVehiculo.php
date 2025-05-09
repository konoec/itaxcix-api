<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\vehiculo\CategoriaVehiculoRepository;

#[ORM\Entity(repositoryClass: CategoriaVehiculoRepository::class)]
#[ORM\Table(name: 'tb_categoria_vehiculo')]
class CategoriaVehiculo {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cate_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'cate_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private string $nombre;

    #[ORM\Column(name: 'cate_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    protected bool $activo = true;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function isActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }
}