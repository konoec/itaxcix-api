<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\TechnicalSpecificationEntity;

class TechnicalSpecificationModel {
    private int $id;
    private VehicleModel $vehicle;
    private ?float $dryWeight = null;
    private ?float $grossWeight = null;
    private ?float $length = null;
    private ?float $height = null;
    private ?float $width = null;
    private ?float $payloadCapacity = null;

    /**
     * @param int $id
     * @param VehicleModel $vehicle
     * @param float|null $dryWeight
     * @param float|null $grossWeight
     * @param float|null $length
     * @param float|null $height
     * @param float|null $width
     * @param float|null $payloadCapacity
     */
    public function __construct(int $id, VehicleModel $vehicle, ?float $dryWeight, ?float $grossWeight, ?float $length, ?float $height, ?float $width, ?float $payloadCapacity)
    {
        $this->id = $id;
        $this->vehicle = $vehicle;
        $this->dryWeight = $dryWeight;
        $this->grossWeight = $grossWeight;
        $this->length = $length;
        $this->height = $height;
        $this->width = $width;
        $this->payloadCapacity = $payloadCapacity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getVehicle(): VehicleModel
    {
        return $this->vehicle;
    }

    public function setVehicle(VehicleModel $vehicle): void
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

    public function toEntity(): TechnicalSpecificationEntity
    {
        $entity = new TechnicalSpecificationEntity();
        $entity->setId($this->id);
        $entity->setVehicle($this->vehicle->toEntity());
        $entity->setDryWeight($this->dryWeight);
        $entity->setGrossWeight($this->grossWeight);
        $entity->setLength($this->length);
        $entity->setHeight($this->height);
        $entity->setWidth($this->width);
        $entity->setPayloadCapacity($this->payloadCapacity);

        return $entity;
    }
}