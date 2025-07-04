<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Reports;

use itaxcix\Core\Handler\TravelReport\TravelReportUseCaseHandler;
use itaxcix\Core\UseCases\TravelReport\TravelReportUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO;
use itaxcix\Shared\Validators\useCases\TravelReport\TravelReportValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TravelReportController extends AbstractController
{
    private TravelReportUseCase $useCase;
    private TravelReportValidator $validator;

    public function __construct(
        TravelReportUseCase $useCase,
        TravelReportValidator $validator
    ) {
        $this->useCase = $useCase;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/reports/travels",
        operationId: "getTravelReport",
        description: "Obtiene un reporte paginado de viajes administrativos con filtros avanzados.",
        summary: "Reporte de viajes administrativos.",
        security: [["bearerAuth" => []]],
        tags: ["Reportes - Viajes"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "startDate", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01"))]
    #[OA\Parameter(name: "endDate", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31"))]
    #[OA\Parameter(name: "citizenId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "driverId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "statusId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "origin", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "destination", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["creationDate", "startDate", "endDate", "citizenId", "driverId", "statusId"], default: "creationDate"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "DESC"))]
    #[OA\Response(
        response: 200,
        description: "Reporte paginado de viajes",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Reporte de viajes obtenido exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "citizenName", type: "string", example: "Juan Pérez"),
                                    new OA\Property(property: "driverName", type: "string", example: "Pedro Gómez"),
                                    new OA\Property(property: "origin", type: "string", example: "Oficina Central"),
                                    new OA\Property(property: "destination", type: "string", example: "Municipalidad"),
                                    new OA\Property(property: "startDate", type: "string", format: "date-time", example: "2024-06-01 08:00:00"),
                                    new OA\Property(property: "endDate", type: "string", format: "date-time", example: "2024-06-01 09:00:00"),
                                    new OA\Property(property: "creationDate", type: "string", format: "date-time", example: "2024-05-31 18:00:00"),
                                    new OA\Property(property: "status", type: "string", example: "Completado")
                                ],
                                type: "object"
                            )
                        ),
                        new OA\Property(
                            property: "pagination",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 20),
                                new OA\Property(property: "total_items", type: "integer", example: 100),
                                new OA\Property(property: "total_pages", type: "integer", example: 5)
                            ],
                            type: "object"
                        )
                    ],
                    type: "object"
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
            $dto = TravelReportRequestDTO::fromArray($queryParams);
            $result = $this->useCase->execute($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el reporte de viajes: ' . $e->getMessage());
        }
    }
}

