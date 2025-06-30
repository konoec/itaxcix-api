<?php

namespace itaxcix\Shared\DTO\useCases\Model;

class ModelResponseDTO
{
    public int $id;
    public string $name;
    public ?int $brandId;
    public ?string $brandName;
    public bool $active;

    public function __construct(int $id, string $name, ?int $brandId, ?string $brandName, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->brandId = $brandId;
        $this->brandName = $brandName;
        $this->active = $active;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrandId(): ?int
    {
        return $this->brandId;
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
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
            'brandId' => $this->brandId,
            'brandName' => $this->brandName,
            'active' => $this->active
        ];
    }
}
