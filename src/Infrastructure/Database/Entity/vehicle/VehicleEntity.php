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

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): void
    {
        $this->licensePlate = $licensePlate;
    }

    public function getModel(): ?ModelEntity
    {
        return $this->model;
    }

    public function setModel(?ModelEntity $model): void
    {
        $this->model = $model;
    }

    public function getColor(): ?ColorEntity
    {
        return $this->color;
    }

    public function setColor(?ColorEntity $color): void
    {
        $this->color = $color;
    }

    public function getManufactureYear(): ?int
    {
        return $this->manufactureYear;
    }

    public function setManufactureYear(?int $manufactureYear): void
    {
        $this->manufactureYear = $manufactureYear;
    }

    public function getSeatCount(): ?int
    {
        return $this->seatCount;
    }

    public function setSeatCount(?int $seatCount): void
    {
        $this->seatCount = $seatCount;
    }

    public function getPassengerCount(): ?int
    {
        return $this->passengerCount;
    }

    public function setPassengerCount(?int $passengerCount): void
    {
        $this->passengerCount = $passengerCount;
    }

    public function getFuelType(): ?FuelTypeEntity
    {
        return $this->fuelType;
    }

    public function setFuelType(?FuelTypeEntity $fuelType): void
    {
        $this->fuelType = $fuelType;
    }

    public function getVehicleClass(): ?VehicleClassEntity
    {
        return $this->vehicleClass;
    }

    public function setVehicleClass(?VehicleClassEntity $vehicleClass): void
    {
        $this->vehicleClass = $vehicleClass;
    }

    public function getCategory(): ?VehicleCategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?VehicleCategoryEntity $category): void
    {
        $this->category = $category;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getVehicleUsers(): Collection
    {
        return $this->vehicleUsers;
    }

    public function setVehicleUsers(Collection $vehicleUsers): void
    {
        $this->vehicleUsers = $vehicleUsers;
    }

}