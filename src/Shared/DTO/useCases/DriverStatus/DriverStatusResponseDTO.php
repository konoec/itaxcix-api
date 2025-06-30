<?php

namespace itaxcix\Shared\DTO\useCases\DriverStatus;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "DriverStatusResponseDTO",
    description: "DTO de respuesta para estado de conductor",
    type: "object"
)]
class DriverStatusResponseDTO
{
    #[OA\Property(
        property: "id",
        description: "ID único del estado de conductor",
        type: "integer",
        example: 1
    )]
    public readonly int $id;

    #[OA\Property(
        property: "name",
        description: "Nombre del estado del conductor",
        type: "string",
        example: "Disponible"
    )]
    public readonly string $name;

    #[OA\Property(
        property: "active",
        description: "Estado activo del registro",
        type: "boolean",
        example: true
    )]
    public readonly bool $active;

    public function __construct(int $id, string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public static function fromModel(\itaxcix\Core\Domain\user\DriverStatusModel $model): self
    {
        return new self(
            id: $model->getId(),
            name: $model->getName(),
            active: $model->isActive()
        );
    }
}
