<?php

namespace itaxcix\Shared\DTO\useCases\FuelType;

use itaxcix\Core\Domain\vehicle\FuelTypeModel;

class FuelTypeResponseDTO
{
    public int $id;
    public string $name;
    public bool $active;

    public function __construct(int $id, string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public static function fromModel(FuelTypeModel $fuelTypeModel): self
    {
        return new self(
            id: $fuelTypeModel->getId(),
            name: $fuelTypeModel->getName(),
            active: $fuelTypeModel->isActive()
        );
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
