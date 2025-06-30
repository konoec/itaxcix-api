<?php

namespace itaxcix\Shared\DTO\useCases\User;

class DriverToCitizenRequestDTO
{
    public int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
