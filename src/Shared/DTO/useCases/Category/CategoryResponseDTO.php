<?php

namespace itaxcix\Shared\DTO\useCases\Category;

use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;

class CategoryResponseDTO
{
    public int $id;
    public ?string $name;
    public bool $active;

    public function __construct(int $id, ?string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public static function fromModel(VehicleCategoryModel $vehicleCategoryModel): self
    {
        return new self(
            id: $vehicleCategoryModel->getId(),
            name: $vehicleCategoryModel->getName(),
            active: $vehicleCategoryModel->isActive()
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
