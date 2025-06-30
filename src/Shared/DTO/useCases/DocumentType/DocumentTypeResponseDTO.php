<?php

namespace itaxcix\Shared\DTO\useCases\DocumentType;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "DocumentTypeResponseDTO",
    description: "DTO de respuesta para tipos de documento",
    type: "object",
    properties: [
        new OA\Property(property: "id", description: "ID del tipo de documento", type: "integer", example: 1),
        new OA\Property(property: "name", description: "Nombre del tipo de documento", type: "string", example: "Cédula de Ciudadanía"),
        new OA\Property(property: "active", description: "Estado activo", type: "boolean", example: true)
    ]
)]
class DocumentTypeResponseDTO
{
    private int $id;
    private string $name;
    private bool $active;

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
