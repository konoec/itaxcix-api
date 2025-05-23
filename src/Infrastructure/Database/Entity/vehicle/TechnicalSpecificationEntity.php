<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_especificacion_tecnica')]
class TechnicalSpecificationEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'espe_id', type: 'integer')]
    private int $id;
    #[ORM\OneToOne(targetEntity: VehicleEntity::class)]
    #[ORM\JoinColumn(name: 'espe_vehiculo_id', referencedColumnName: 'vehi_id')]
    private VehicleEntity $vehicle;
    #[ORM\Column(name: 'espe_peso_seco', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $dryWeight = null;
    #[ORM\Column(name: 'espe_peso_bruto', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $grossWeight = null;
    #[ORM\Column(name: 'espe_longitud', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $length = null;
    #[ORM\Column(name: 'espe_altura', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $height = null;
    #[ORM\Column(name: 'espe_anchura', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $width = null;
    #[ORM\Column(name: 'espe_carga_util', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $payloadCapacity = null;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getVehicle(): VehicleEntity
    {
        return $this->vehicle;
    }

    public function setVehicle(VehicleEntity $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function getDryWeight(): ?float
    {
        return $this->dryWeight;
    }

    public function setDryWeight(?float $dryWeight): void
    {
        $this->dryWeight = $dryWeight;
    }

    public function getGrossWeight(): ?float
    {
        return $this->grossWeight;
    }

    public function setGrossWeight(?float $grossWeight): void
    {
        $this->grossWeight = $grossWeight;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): void
    {
        $this->length = $length;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): void
    {
        $this->height = $height;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): void
    {
        $this->width = $width;
    }

    public function getPayloadCapacity(): ?float
    {
        return $this->payloadCapacity;
    }

    public function setPayloadCapacity(?float $payloadCapacity): void
    {
        $this->payloadCapacity = $payloadCapacity;
    }

}