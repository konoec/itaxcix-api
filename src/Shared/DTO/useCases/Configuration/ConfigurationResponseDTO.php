<?php

namespace itaxcix\Shared\DTO\useCases\Configuration;

use itaxcix\Core\Domain\configuration\ConfigurationModel;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ConfigurationResponseDTO",
    description: "DTO de respuesta para configuraciones del sistema",
    type: "object"
)]
class ConfigurationResponseDTO
{
    #[OA\Property(
        property: "id",
        description: "ID de la configuración",
        type: "integer",
        example: 1
    )]
    private int $id;

    #[OA\Property(
        property: "key",
        description: "Clave única de configuración",
        type: "string",
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
        example: true
    )]
    private bool $active;

    public function __construct(
        int $id,
        string $key,
        string $value,
        bool $active
    ) {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
        $this->active = $active;
    }

    public static function fromModel(ConfigurationModel $model): self
    {
        return new self(
            id: $model->getId() ?? 0, // Usar 0 como fallback si el id es null
            key: $model->getKey(),
            value: $model->getValue(),
            active: $model->isActive()
        );
    }

    public function getId(): int
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
