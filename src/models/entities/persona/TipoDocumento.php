<?php

namespace itaxcix\models\entities\persona;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\repositories\persona\TipoDocumentoRepository;

#[ORM\Entity(repositoryClass: TipoDocumentoRepository::class)]
#[ORM\Table(name: 'tb_tipo_documento')]
class TipoDocumento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'tipo_id')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, name: 'tipo_nombre')]
    private string $nombre;

    #[ORM\Column(type: 'boolean', name: 'tipo_activo', options: ['default' => true])]
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