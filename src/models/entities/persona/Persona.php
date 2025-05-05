<?php

namespace itaxcix\models\entities\persona;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\repositories\persona\PersonaRepository;

#[ORM\Entity(repositoryClass: PersonaRepository::class)]
#[ORM\Table(name: 'tb_persona')]
class Persona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'pers_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'pers_nombre', type: 'string', length: 100, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'pers_apellido', type: 'string', length: 100, nullable: true)]
    private ?string $apellido = null;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class)]
    #[ORM\JoinColumn(name: 'pers_tipo_documento_id', referencedColumnName: 'tipo_id', nullable: false)]
    private ?TipoDocumento $tipoDocumento = null;

    #[ORM\Column(name: 'pers_documento', type: 'string', length: 20, nullable: false, unique: true)]
    private string $documento;

    #[ORM\Column(name: 'pers_imagen', type: 'string', length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(name: 'pers_activo', type: 'boolean', options: ['default' => true])]
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): void
    {
        $this->apellido = $apellido;
    }

    public function getTipoDocumento(): ?TipoDocumento
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?TipoDocumento $tipoDocumento): void
    {
        $this->tipoDocumento = $tipoDocumento;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(?string $documento): void
    {
        $this->documento = $documento;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): void
    {
        $this->imagen = $imagen;
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