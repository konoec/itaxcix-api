<?php

namespace itaxcix\Shared\DTO\useCases\Incident;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "GetUserIncidentsResponseDTO",
    description: "DTO de respuesta para los incidentes de un usuario",
    type: "object"
)]
class GetUserIncidentsResponseDTO
{
    #[OA\Property(
        property: "incidents",
        type: "array",
        description: "Lista de incidentes",
        items: new OA\Items(
            properties: [
                new OA\Property(property: "id", type: "integer", description: "ID del incidente", example: 1),
                new OA\Property(property: "userId", type: "integer", description: "ID del usuario", example: 1),
                new OA\Property(property: "userName", type: "string", description: "Nombre completo del usuario", example: "Juan Pérez"),
                new OA\Property(property: "travelId", type: "integer", description: "ID del viaje", example: 123),
                new OA\Property(property: "typeId", type: "integer", description: "ID del tipo de incidencia", example: 1),
                new OA\Property(property: "typeName", type: "string", description: "Nombre del tipo de incidencia", example: "Problema técnico"),
                new OA\Property(property: "comment", type: "string", description: "Comentario del incidente", example: "El vehículo se detuvo"),
                new OA\Property(property: "active", type: "boolean", description: "Estado activo", example: true)
            ],
            type: "object"
        )
    )]
    public array $incidents;

    #[OA\Property(property: "pagination", ref: "#/components/schemas/PaginationInfo")]
    public array $pagination;

    public function __construct(array $incidents, array $pagination)
    {
        $this->incidents = $incidents;
        $this->pagination = $pagination;
    }
}
