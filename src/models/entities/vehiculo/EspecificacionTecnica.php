<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_especificacion_tecnica')]
class EspecificacionTecnica {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'espe_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehiculo::class)]
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
}