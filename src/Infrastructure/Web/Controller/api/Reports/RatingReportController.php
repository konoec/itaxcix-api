<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Reports;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO;
use itaxcix\Shared\Validators\useCases\RatingReport\RatingReportValidator;
use itaxcix\Core\Handler\RatingReport\RatingReportUseCaseHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RatingReportController extends AbstractController
{
    private RatingReportUseCaseHandler $handler;
    private RatingReportValidator $validator;

    public function __construct(
        RatingReportUseCaseHandler $handler,
        RatingReportValidator $validator
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/reports/ratings",
        operationId: "getRatingReport",
        description: "Obtiene un reporte paginado de calificaciones con filtros avanzados.",
        summary: "Reporte de calificaciones.",
        security: [["bearerAuth" => []]],
        tags: ["Reportes - Calificaciones"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "raterId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "ratedId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "travelId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "minScore", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "maxScore", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "comment", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "raterId", "ratedId", "travelId", "score"], default: "id"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "DESC"))]
    #[OA\Response(
        response: 200,
        description: "Reporte paginado de calificaciones",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Reporte de calificaciones obtenido exitosamente"),
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
                                    new OA\Property(property: "raterId", type: "integer", example: 10),
                                    new OA\Property(property: "raterName", type: "string", example: "Juan Pérez"),
                                    new OA\Property(property: "ratedId", type: "integer", example: 11),
                                    new OA\Property(property: "ratedName", type: "string", example: "Pedro Gómez"),
                                    new OA\Property(property: "travelId", type: "integer", example: 5),
                                    new OA\Property(property: "score", type: "integer", example: 5),
                                    new OA\Property(property: "comment", type: "string", example: "Excelente servicio")
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
            $dto = RatingReportRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el reporte de calificaciones: ' . $e->getMessage());
        }
    }
}

