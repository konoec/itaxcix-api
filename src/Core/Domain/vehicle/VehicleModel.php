<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;

class VehicleModel {
    private int $id;
    private string $licensePlate;
    private ?ModelModel $model = null;
    private ?ColorModel $color = null;
    private ?int $manufactureYear = null;
    private ?int $seatCount = null;
    private ?int $passengerCount = null;
    private ?FuelTypeModel $fuelType = null;
    private ?VehicleClassModel $vehicleClass = null;
    private ?VehicleCategoryModel $category = null;
    private bool $active = true;

    /**
     * @param int $id
     * @param string $licensePlate
     * @param ModelModel|null $model
     * @param ColorModel|null $color
     * @param int|null $manufactureYear
     * @param int|null $seatCount
     * @param int|null $passengerCount
     * @param FuelTypeModel|null $fuelType
     * @param VehicleClassModel|null $vehicleClass
     * @param VehicleCategoryModel|null $category
     * @param bool $active
     */
    public function __construct(int $id, string $licensePlate, ?ModelModel $model, ?ColorModel $color, ?int $manufactureYear, ?int $seatCount, ?int $passengerCount, ?FuelTypeModel $fuelType, ?VehicleClassModel $vehicleClass, ?VehicleCategoryModel $category, bool $active)
    {
        $this->id = $id;
        $this->licensePlate = $licensePlate;
        $this->model = $model;
        $this->color = $color;
        $this->manufactureYear = $manufactureYear;
        $this->seatCount = $seatCount;
        $this->passengerCount = $passengerCount;
        $this->fuelType = $fuelType;
        $this->vehicleClass = $vehicleClass;
        $this->category = $category;
        $this->active = $active;
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

    public function getModel(): ?ModelModel
    {
        return $this->model;
    }

    public function setModel(?ModelModel $model): void
    {
        $this->model = $model;
    }

    public function getColor(): ?ColorModel
    {
        return $this->color;
    }

    public function setColor(?ColorModel $color): void
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

    public function getFuelType(): ?FuelTypeModel
    {
        return $this->fuelType;
    }

    public function setFuelType(?FuelTypeModel $fuelType): void
    {
        $this->fuelType = $fuelType;
    }

    public function getVehicleClass(): ?VehicleClassModel
    {
        return $this->vehicleClass;
    }

    public function setVehicleClass(?VehicleClassModel $vehicleClass): void
    {
        $this->vehicleClass = $vehicleClass;
    }

    public function getCategory(): ?VehicleCategoryModel
    {
        return $this->category;
    }

    public function setCategory(?VehicleCategoryModel $category): void
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

    public function toEntity(): VehicleEntity
    {
        $vehicleEntity = new VehicleEntity();
        $vehicleEntity->setId($this->id);
        $vehicleEntity->setLicensePlate($this->licensePlate);
        $vehicleEntity->setModel($this->model?->toEntity());
        $vehicleEntity->setColor($this->color?->toEntity());
        $vehicleEntity->setManufactureYear($this->manufactureYear);
        $vehicleEntity->setSeatCount($this->seatCount);
        $vehicleEntity->setPassengerCount($this->passengerCount);
        $vehicleEntity->setFuelType($this->fuelType?->toEntity());
        $vehicleEntity->setVehicleClass($this->vehicleClass?->toEntity());
        $vehicleEntity->setCategory($this->category?->toEntity());
        $vehicleEntity->setActive($this->active);

        return $vehicleEntity;
    }
}