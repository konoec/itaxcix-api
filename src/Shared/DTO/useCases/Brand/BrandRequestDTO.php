<?php

namespace itaxcix\Shared\DTO\useCases\Brand;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BrandRequestDTO",
    description: "DTO para solicitudes de marca de vehículo",
    type: "object",
    required: ["name"]
)]
class BrandRequestDTO
{
    #[OA\Property(
        property: "name",
        description: "Nombre de la marca",
        type: "string",
        maxLength: 50,
        example: "Toyota"
    )]
    public string $name;

    #[OA\Property(
        property: "active",
        description: "Estado activo de la marca",
        type: "boolean",
        default: true,
        example: true
    )]
    public bool $active;

    public function __construct(string $name, bool $active = true)
    {
        $this->name = $name;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
