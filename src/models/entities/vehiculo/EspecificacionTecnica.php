<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\vehiculo\EspecificacionTecnicaRepository;

#[ORM\Entity(repositoryClass: EspecificacionTecnicaRepository::class)]
#[ORM\Table(name: 'tb_especificacion_tecnica')]
class EspecificacionTecnica {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'espe_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Vehiculo::class)]
    #[ORM\JoinColumn(name: 'espe_vehiculo_id', referencedColumnName: 'vehi_id')]
    private ?Vehiculo $vehiculo = null;

    #[ORM\Column(name: 'espe_peso_seco', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $pesoSeco = null;

    #[ORM\Column(name: 'espe_peso_bruto', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $pesoBruto = null;

    #[ORM\Column(name: 'espe_longitud', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $longitud = null;

    #[ORM\Column(name: 'espe_altura', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $altura = null;

    #[ORM\Column(name: 'espe_anchura', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $anchura = null;

    #[ORM\Column(name: 'espe_carga_util', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $cargaUtil = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getVehiculo(): ?Vehiculo {
        return $this->vehiculo;
    }

    public function setVehiculo(?Vehiculo $vehiculo): void {
        $this->vehiculo = $vehiculo;
    }

    public function getPesoSeco(): ?float {
        return $this->pesoSeco;
    }

    public function setPesoSeco(?float $pesoSeco): void {
        $this->pesoSeco = $pesoSeco;
    }

    public function getPesoBruto(): ?float {
        return $this->pesoBruto;
    }

    public function setPesoBruto(?float $pesoBruto): void {
        $this->pesoBruto = $pesoBruto;
    }

    public function getLongitud(): ?float {
        return $this->longitud;
    }

    public function setLongitud(?float $longitud): void {
        $this->longitud = $longitud;
    }

    public function getAltura(): ?float {
        return $this->altura;
    }

    public function setAltura(?float $altura): void {
        $this->altura = $altura;
    }

    public function getAnchura(): ?float {
        return $this->anchura;
    }

    public function setAnchura(?float $anchura): void {
        $this->anchura = $anchura;
    }

    public function getCargaUtil(): ?float {
        return $this->cargaUtil;
    }

    public function setCargaUtil(?float $cargaUtil): void {
        $this->cargaUtil = $cargaUtil;
    }
}