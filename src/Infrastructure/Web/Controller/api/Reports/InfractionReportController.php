<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Reports;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportRequestDTO;
use itaxcix\Shared\Validators\useCases\InfractionReport\InfractionReportValidator;
use itaxcix\Core\Handler\InfractionReport\InfractionReportUseCaseHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InfractionReportController extends AbstractController
{
    private InfractionReportUseCaseHandler $handler;
    private InfractionReportValidator $validator;

    public function __construct(
        InfractionReportUseCaseHandler $handler,
        InfractionReportValidator $validator
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/reports/infractions",
        operationId: "getInfractionReport",
        description: "Obtiene un reporte paginado de infracciones con filtros avanzados.",
        summary: "Reporte de infracciones.",
        security: [["bearerAuth" => []]],
        tags: ["Reportes - Infracciones"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "userId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "severityId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "statusId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "dateFrom", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date"))]
    #[OA\Parameter(name: "dateTo", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date"))]
    #[OA\Parameter(name: "description", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "userId", "severityId", "statusId", "date"], default: "id"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "DESC"))]
    #[OA\Response(
        response: 200,
        description: "Reporte paginado de infracciones",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Reporte de infracciones obtenido exitosamente"),
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
                                    new OA\Property(property: "severityId", type: "integer", example: 2),
                                    new OA\Property(property: "severityName", type: "string", example: "Grave"),
                                    new OA\Property(property: "statusId", type: "integer", example: 1),
                                    new OA\Property(property: "statusName", type: "string", example: "Pendiente"),
                                    new OA\Property(property: "date", type: "string", format: "date-time", example: "2025-06-29 10:00:00"),
                                    new OA\Property(property: "description", type: "string", example: "No respetó la luz roja")
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
            $dto = InfractionReportRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el reporte de infracciones: ' . $e->getMessage());
        }
    }
}

