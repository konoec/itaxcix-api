<?php

namespace itaxcix\Shared\DTO\useCases\AuditLog;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AuditLogResponseDTO",
    description: "DTO de respuesta para detalle de registro de auditoría",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "affectedTable", type: "string", example: "tb_usuario"),
        new OA\Property(property: "operation", type: "string", example: "UPDATE"),
        new OA\Property(property: "systemUser", type: "string", example: "postgres"),
        new OA\Property(property: "date", type: "string", format: "date-time", example: "2025-06-29 10:00:00"),
        new OA\Property(property: "previousData", type: "object", nullable: true),
        new OA\Property(property: "newData", type: "object", nullable: true)
    ]
)]
class AuditLogResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $affectedTable,
        public readonly string $operation,
        public readonly string $systemUser,
        public readonly string $date,
        public readonly ?array $previousData,
        public readonly ?array $newData
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'affectedTable' => $this->affectedTable,
            'operation' => $this->operation,
            'systemUser' => $this->systemUser,
            'date' => $this->date,
            'previousData' => $this->previousData,
            'newData' => $this->newData
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            affectedTable: $data['affectedTable'],
            operation: $data['operation'],
            systemUser: $data['systemUser'],
            date: $data['date'],
            previousData: $data['previousData'] ?? null,
            newData: $data['newData'] ?? null
        );
    }
}
