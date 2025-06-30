<?php

namespace itaxcix\Shared\DTO\useCases\User;

class DriverToCitizenResponseDTO
{
    public int $userId;
    public string $status;
    public string $message;
    public ?int $citizenProfileId;

    public function __construct(int $userId, string $status, string $message, ?int $citizenProfileId = null)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->message = $message;
        $this->citizenProfileId = $citizenProfileId;
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

    public function getCitizenProfileId(): ?int
    {
        return $this->citizenProfileId;
    }
}
