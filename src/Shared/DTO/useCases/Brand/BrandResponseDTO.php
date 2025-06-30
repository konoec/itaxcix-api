<?php

namespace itaxcix\Shared\DTO\useCases\Brand;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BrandResponseDTO",
    description: "DTO de respuesta para marca de vehículo",
    type: "object"
)]
class BrandResponseDTO
{
    #[OA\Property(
        property: "id",
        description: "ID único de la marca",
        type: "integer",
        example: 1
    )]
    public int $id;

    #[OA\Property(
        property: "name",
        description: "Nombre de la marca",
        type: "string",
        example: "Toyota"
    )]
    public string $name;

    #[OA\Property(
        property: "active",
        description: "Estado activo de la marca",
        type: "boolean",
        example: true
    )]
    public bool $active;

    public function __construct(int $id, string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
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
