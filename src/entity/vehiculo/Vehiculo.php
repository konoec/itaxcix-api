<?php

namespace itaxcix\entity\vehiculo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_vehiculo')]
class Vehiculo {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'vehi_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'vehi_placa', type: 'string', length: 10, unique: true)]
    private string $placa;

    #[ORM\ManyToOne(targetEntity: Modelo::class)]
    #[ORM\JoinColumn(name: 'vehi_modelo_id', referencedColumnName: 'mode_id')]
    private ?Modelo $modelo = null;

    #[ORM\ManyToOne(targetEntity: Color::class)]
    #[ORM\JoinColumn(name: 'vehi_color_id', referencedColumnName: 'colo_id')]
    private ?Color $color = null;

    #[ORM\Column(name: 'vehi_anio_fabric', type: 'integer', nullable: true)]
    private ?int $anioFabricacion = null;

    #[ORM\Column(name: 'vehi_num_asientos', type: 'integer', nullable: true)]
    private ?int $numeroAsientos = null;

    #[ORM\Column(name: 'vehi_num_pasajeros', type: 'integer', nullable: true)]
    private ?int $numeroPasajeros = null;

    #[ORM\ManyToOne(targetEntity: TipoCombustible::class)]
    #[ORM\JoinColumn(name: 'vehi_tipo_combustible_id', referencedColumnName: 'tipo_id')]
    private ?TipoCombustible $tipoCombustible = null;

    #[ORM\ManyToOne(targetEntity: ClaseVehiculo::class)]
    #[ORM\JoinColumn(name: 'vehi_clase_id', referencedColumnName: 'clas_id')]
    private ?ClaseVehiculo $clase = null;

    #[ORM\ManyToOne(targetEntity: CategoriaVehiculo::class)]
    #[ORM\JoinColumn(name: 'vehi_categoria_id', referencedColumnName: 'cate_id')]
    private ?CategoriaVehiculo $categoria = null;

    #[ORM\Column(type: 'boolean', name: 'vehi_activo', options: ['default' => true])]
    private bool $activo = true;
}