<?php

namespace itaxcix\models\entities\tuc;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\tuc\TipoTramiteRepository;

#[ORM\Entity(repositoryClass: TipoTramiteRepository::class)]
#[ORM\Table(name: 'tb_tipo_tramite')]
class TipoTramite {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tipo_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tipo_nombre', type: 'string', length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'tipo_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
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