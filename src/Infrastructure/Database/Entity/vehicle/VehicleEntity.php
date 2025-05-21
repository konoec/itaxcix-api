<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_vehiculo')]
class VehicleEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'vehi_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'vehi_placa', type: 'string', length: 10, unique: true)]
    private string $licensePlate;
    #[ORM\ManyToOne(targetEntity: ModelEntity::class)]
    #[ORM\JoinColumn(name: 'vehi_modelo_id', referencedColumnName: 'mode_id')]
    private ?ModelEntity $model = null;
    #[ORM\ManyToOne(targetEntity: ColorEntity::class)]
    #[ORM\JoinColumn(name: 'vehi_color_id', referencedColumnName: 'colo_id')]
    private ?ColorEntity $color = null;
    #[ORM\Column(name: 'vehi_anio_fabric', type: 'integer', nullable: true)]
    private ?int $manufactureYear = null;
    #[ORM\Column(name: 'vehi_num_asientos', type: 'integer', nullable: true)]
    private ?int $seatCount = null;
    #[ORM\Column(name: 'vehi_num_pasajeros', type: 'integer', nullable: true)]
    private ?int $passengerCount = null;
    #[ORM\ManyToOne(targetEntity: FuelTypeEntity::class)]
    #[ORM\JoinColumn(name: 'vehi_tipo_combustible_id', referencedColumnName: 'tipo_id')]
    private ?FuelTypeEntity $fuelType = null;
    #[ORM\ManyToOne(targetEntity: VehicleClassEntity::class)]
    #[ORM\JoinColumn(name: 'vehi_clase_id', referencedColumnName: 'clas_id')]
    private ?VehicleClassEntity $vehicleClass = null;
    #[ORM\ManyToOne(targetEntity: VehicleCategoryEntity::class)]
    #[ORM\JoinColumn(name: 'vehi_categoria_id', referencedColumnName: 'cate_id')]
    private ?VehicleCategoryEntity $category = null;
    #[ORM\Column(name: 'vehi_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
    #[ORM\OneToMany(
        targetEntity: VehicleUserEntity::class,
        mappedBy: 'vehicle',
        orphanRemoval: true
    )]
    private Collection $vehicleUsers;
}