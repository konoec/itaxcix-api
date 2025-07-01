<?php

namespace itaxcix\Shared\DTO\useCases\User;

class CitizenToDriverRequestDTO
{
    public int $userId;
    public string $plateValue;

    public function __construct(int $userId, string $plateValue)
    {
        $this->userId = $userId;
        $this->plateValue = $plateValue;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPlateValue(): string
    {
        return $this->plateValue;
    }
}
