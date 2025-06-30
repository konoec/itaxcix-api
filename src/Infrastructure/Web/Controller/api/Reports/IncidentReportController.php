<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Reports;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO;
use itaxcix\Shared\Validators\useCases\IncidentReport\IncidentReportValidator;
use itaxcix\Core\Handler\IncidentReport\IncidentReportUseCaseHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IncidentReportController extends AbstractController
{
    private IncidentReportUseCaseHandler $handler;
    private IncidentReportValidator $validator;

    public function __construct(
        IncidentReportUseCaseHandler $handler,
        IncidentReportValidator $validator
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/reports/incidents",
        operationId: "getIncidentReport",
        description: "Obtiene un reporte paginado de incidentes con filtros avanzados.",
        summary: "Reporte de incidentes.",
        security: [["bearerAuth" => []]],
        tags: ["Reportes - Incidentes"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "userId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "travelId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "typeId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "active", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "comment", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "userId", "travelId", "typeId", "active"], default: "id"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "DESC"))]
    #[OA\Response(
        response: 200,
        description: "Reporte paginado de incidentes",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Reporte de incidentes obtenido exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "userId", type: "integer", example: 10),
                                    new OA\Property(property: "userName", type: "string", example: "Juan Pérez"),
                                    new OA\Property(property: "travelId", type: "integer", example: 5),
                                    new OA\Property(property: "typeId", type: "integer", example: 2),
                                    new OA\Property(property: "typeName", type: "string", example: "Incidente grave"),
                                    new OA\Property(property: "comment", type: "string", example: "El conductor no llegó a tiempo"),
                                    new OA\Property(property: "active", type: "boolean", example: true)
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 20),
                                new OA\Property(property: "total_items", type: "integer", example: 100),
                                new OA\Property(property: "total_pages", type: "integer", example: 5)
                            ]
                        )
                    ]
                )
            ],
            type: "object"
        )
    )]
    public function getReport(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $validationErrors = $this->validator->validateFilters($queryParams);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }
            $dto = IncidentReportRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el reporte de incidentes: ' . $e->getMessage());
        }
    }
}

