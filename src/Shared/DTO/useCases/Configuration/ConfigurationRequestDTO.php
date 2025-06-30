<?php

namespace itaxcix\Shared\DTO\useCases\Configuration;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ConfigurationRequestDTO",
    description: "DTO para crear/actualizar configuraciones del sistema",
    type: "object",
    required: ["key", "value"]
)]
class ConfigurationRequestDTO
{
    #[OA\Property(
        property: "id",
        description: "ID de la configuración (solo para actualización)",
        type: "integer",
        example: 1,
        nullable: true
    )]
    private ?int $id;

    #[OA\Property(
        property: "key",
        description: "Clave única de configuración",
        type: "string",
        maxLength: 255,
        example: "app.maintenance_mode"
    )]
    private string $key;

    #[OA\Property(
        property: "value",
        description: "Valor de la configuración",
        type: "string",
        example: "false"
    )]
    private string $value;

    #[OA\Property(
        property: "active",
        description: "Estado activo de la configuración",
        type: "boolean",
        default: true,
        example: true
    )]
    private bool $active;

    public function __construct(?int $id, string $key, string $value, bool $active = true)
    {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
        $this->active = $active;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
