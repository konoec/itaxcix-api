<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\vehiculo\ColorRepository;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
#[ORM\Table(name: 'tb_color')]
class Color {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'colo_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'colo_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $nombre;

    #[ORM\Column(name: 'colo_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $activo = true;

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