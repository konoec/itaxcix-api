<?php

namespace itaxcix\Shared\DTO\useCases\Color;

use itaxcix\Core\Domain\vehicle\ColorModel;

class ColorResponseDTO
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

    public static function fromModel(ColorModel $colorModel): self
    {
        return new self(
            id: $colorModel->getId(),
            name: $colorModel->getName(),
            active: $colorModel->isActive()
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
