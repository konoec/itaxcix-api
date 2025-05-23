<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleUserEntity;

class VehicleUserModel {
    private int $id;
    private ?UserModel $user = null;
    private ?VehicleModel $vehicle = null;
    private bool $active = true;

    /**
     * @param int $id
     * @param UserModel|null $user
     * @param VehicleModel|null $vehicle
     * @param bool $active
     */
    public function __construct(int $id, ?UserModel $user, ?VehicleModel $vehicle, bool $active)
    {
        $this->id = $id;
        $this->user = $user;
        $this->vehicle = $vehicle;
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

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(?UserModel $user): void
    {
        $this->user = $user;
    }

    public function getVehicle(): ?VehicleModel
    {
        return $this->vehicle;
    }

    public function setVehicle(?VehicleModel $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function toEntity(): VehicleUserEntity
    {
        $entity = new VehicleUserEntity();
        $entity->setId($this->id);
        $entity->setUser($this->user?->toEntity());
        $entity->setVehicle($this->vehicle?->toEntity());
        $entity->setActive($this->active);

        return $entity;
    }
}