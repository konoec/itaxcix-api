<?php

namespace itaxcix\Shared\DTO\useCases\Model;

class ModelRequestDTO
{
    public string $name;
    public ?int $brandId = null;
    public bool $active = true;

    public function __construct(string $name, ?int $brandId = null, bool $active = true)
    {
        $this->name = $name;
        $this->brandId = $brandId;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrandId(): ?int
    {
        return $this->brandId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
