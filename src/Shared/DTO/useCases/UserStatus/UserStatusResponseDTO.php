<?php

namespace itaxcix\Shared\DTO\useCases\UserStatus;

class UserStatusResponseDTO
{
    private ?int $id;
    private string $name;
    private bool $active;

    public function __construct(?int $id, string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active
        ];
    }
}
