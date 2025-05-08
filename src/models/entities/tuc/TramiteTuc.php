<?php

namespace itaxcix\models\entities\tuc;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\entities\ubicacion\Distrito;
use itaxcix\models\entities\usuario\Usuario;
use itaxcix\models\entities\vehiculo\Vehiculo;
use itaxcix\repositories\tuc\TramiteTucRepository;

#[ORM\Entity(repositoryClass: TramiteTucRepository::class)]
#[ORM\Table(name: 'tb_tramite_tuc')]
class TramiteTuc {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tram_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tram_codigo', type: 'string', length: 8, nullable: true)]
    private ?string $codigo = null;

    #[ORM\ManyToOne(targetEntity: Vehiculo::class)]
    #[ORM\JoinColumn(name: 'tram_vehiculo_id', referencedColumnName: 'vehi_id')]
    private ?Vehiculo $vehiculo = null;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'tram_empresa_id', referencedColumnName: 'empr_id')]
    private ?Empresa $empresa = null;

    #[ORM\ManyToOne(targetEntity: Distrito::class)]
    #[ORM\JoinColumn(name: 'tram_distrito_id', referencedColumnName: 'dist_id')]
    private ?Distrito $distrito = null;

    #[ORM\ManyToOne(targetEntity: EstadoTuc::class)]
    #[ORM\JoinColumn(name: 'tram_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoTuc $estado = null;

    #[ORM\ManyToOne(targetEntity: TipoTramite::class)]
    #[ORM\JoinColumn(name: 'tram_tipo_id', referencedColumnName: 'tipo_id')]
    private ?TipoTramite $tipo = null;

    #[ORM\ManyToOne(targetEntity: ModalidadTuc::class)]
    #[ORM\JoinColumn(name: 'tram_modalidad_id', referencedColumnName: 'moda_id')]
    private ?ModalidadTuc $modalidad = null;

    #[ORM\Column(name: 'tram_fecha_tramite', type: 'date', nullable: true)]
    private ?\DateTime $fechaTramite = null;

    #[ORM\Column(name: 'tram_fecha_emision', type: 'date', nullable: true)]
    private ?\DateTime $fechaEmision = null;

    #[ORM\Column(name: 'tram_fecha_caducidad', type: 'date', nullable: true)]
    private ?\DateTime $fechaCaducidad = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getVehiculo(): ?Vehiculo
    {
        return $this->vehiculo;
    }

    public function setVehiculo(?Vehiculo $vehiculo): void
    {
        $this->vehiculo = $vehiculo;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): void
    {
        $this->empresa = $empresa;
    }

    public function getDistrito(): ?Distrito
    {
        return $this->distrito;
    }

    public function setDistrito(?Distrito $distrito): void
    {
        $this->distrito = $distrito;
    }

    public function getEstado(): ?EstadoTuc
    {
        return $this->estado;
    }

    public function setEstado(?EstadoTuc $estado): void
    {
        $this->estado = $estado;
    }

    public function getTipo(): ?TipoTramite
    {
        return $this->tipo;
    }

    public function setTipo(?TipoTramite $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getModalidad(): ?ModalidadTuc
    {
        return $this->modalidad;
    }

    public function setModalidad(?ModalidadTuc $modalidad): void
    {
        $this->modalidad = $modalidad;
    }

    public function getFechaTramite(): ?\DateTime
    {
        return $this->fechaTramite;
    }

    public function setFechaTramite(?\DateTime $fechaTramite): void
    {
        $this->fechaTramite = $fechaTramite;
    }

    public function getFechaEmision(): ?\DateTime
    {
        return $this->fechaEmision;
    }

    public function setFechaEmision(?\DateTime $fechaEmision): void
    {
        $this->fechaEmision = $fechaEmision;
    }

    public function getFechaCaducidad(): ?\DateTime
    {
        return $this->fechaCaducidad;
    }

    public function setFechaCaducidad(?\DateTime $fechaCaducidad): void
    {
        $this->fechaCaducidad = $fechaCaducidad;
    }
}