<?php

namespace itaxcix\Shared\DTO\useCases\User;

class CitizenToDriverRequestDTO
{
    public int $userId;
    public int $vehicleId;

    public function __construct(int $userId, int $vehicleId)
    {
        $this->userId = $userId;
        $this->vehicleId = $vehicleId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }
}
