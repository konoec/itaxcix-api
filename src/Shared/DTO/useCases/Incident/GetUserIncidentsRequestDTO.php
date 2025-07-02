<?php

namespace itaxcix\Shared\DTO\useCases\Incident;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "GetUserIncidentsRequestDTO",
    description: "DTO para obtener incidentes de un usuario",
    type: "object"
)]
class GetUserIncidentsRequestDTO
{
    #[OA\Property(property: "userId", type: "integer", description: "ID del usuario", example: 1)]
    public int $userId;

    #[OA\Property(property: "travelId", type: "integer", description: "ID del viaje (opcional)", example: 123, nullable: true)]
    public ?int $travelId;

    #[OA\Property(property: "typeId", type: "integer", description: "ID del tipo de incidencia (opcional)", example: 1, nullable: true)]
    public ?int $typeId;

    #[OA\Property(property: "active", type: "boolean", description: "Estado activo del incidente (opcional)", example: true, nullable: true)]
    public ?bool $active;

    #[OA\Property(property: "comment", type: "string", description: "Búsqueda en comentarios (opcional)", example: "problema", nullable: true)]
    public ?string $comment;

    #[OA\Property(property: "page", type: "integer", description: "Número de página", example: 1, minimum: 1)]
    public int $page = 1;

    #[OA\Property(property: "perPage", type: "integer", description: "Elementos por página", example: 10, minimum: 1, maximum: 100)]
    public int $perPage = 10;

    #[OA\Property(property: "sortBy", type: "string", description: "Campo de ordenamiento", example: "id", enum: ["id", "travelId", "typeId", "active"])]
    public string $sortBy = 'id';

    #[OA\Property(property: "sortDirection", type: "string", description: "Dirección del ordenamiento", example: "DESC", enum: ["ASC", "DESC"])]
    public string $sortDirection = 'DESC';

    public function __construct(
        int $userId,
        ?int $travelId = null,
        ?int $typeId = null,
        ?bool $active = null,
        ?string $comment = null,
        int $page = 1,
        int $perPage = 10,
        string $sortBy = 'id',
        string $sortDirection = 'DESC'
    ) {
        $this->userId = $userId;
        $this->travelId = $travelId;
        $this->typeId = $typeId;
        $this->active = $active;
        $this->comment = $comment;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
    }
}
