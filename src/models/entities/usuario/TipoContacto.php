<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\usuario\TipoContactoRepository;

#[ORM\Entity(repositoryClass: TipoContactoRepository::class)]
#[ORM\Table(name: 'tb_tipo_contacto')]
class TipoContacto {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tipo_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tipo_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $nombre;

    #[ORM\Column(name: 'tipo_activo', type: 'boolean', nullable: false, options: ['default' => true])]
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