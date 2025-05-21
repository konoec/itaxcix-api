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
}