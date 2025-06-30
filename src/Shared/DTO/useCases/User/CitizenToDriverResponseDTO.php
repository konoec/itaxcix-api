<?php

namespace itaxcix\Shared\DTO\useCases\User;

class CitizenToDriverResponseDTO
{
    public int $userId;
    public string $status;
    public string $message;
    public ?int $driverProfileId;

    public function __construct(int $userId, string $status, string $message, ?int $driverProfileId = null)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->message = $message;
        $this->driverProfileId = $driverProfileId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDriverProfileId(): ?int
    {
        return $this->driverProfileId;
    }
}
