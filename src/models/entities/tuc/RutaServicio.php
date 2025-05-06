<?php

namespace itaxcix\models\entities\tuc;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\tuc\RutaServicioRepository;

#[ORM\Entity(repositoryClass: RutaServicioRepository::class)]
#[ORM\Table(name: 'tb_ruta_servicio')]
class RutaServicio {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ruta_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TramiteTuc::class)]
    #[ORM\JoinColumn(name: 'ruta_tram_id', referencedColumnName: 'tram_id')]
    private ?TramiteTuc $tramite = null;

    #[ORM\Column(name: 'ruta_tipo_servicio', type: 'string', length: 50, nullable: true)]
    private ?string $tipoServicio = null;

    #[ORM\Column(name: 'ruta_texto', type: 'text', nullable: true)]
    private ?string $texto = null;

    #[ORM\Column(name: 'ruta_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTramite(): ?TramiteTuc
    {
        return $this->tramite;
    }

    public function setTramite(?TramiteTuc $tramite): void
    {
        $this->tramite = $tramite;
    }

    public function getTipoServicio(): ?string
    {
        return $this->tipoServicio;
    }

    public function setTipoServicio(?string $tipoServicio): void
    {
        $this->tipoServicio = $tipoServicio;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): void
    {
        $this->texto = $texto;
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