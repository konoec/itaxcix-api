<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\usuario\EstadoUsuarioRepository;

#[ORM\Entity(repositoryClass: EstadoUsuarioRepository::class)]
#[ORM\Table(name: 'tb_estado_usuario')]
class EstadoUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'esta_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'esta_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'esta_activo', type: 'boolean', options: ['default' => true])]
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