<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Reports;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO;
use itaxcix\Shared\Validators\useCases\VehicleReport\VehicleReportValidator;
use itaxcix\Core\Handler\VehicleReport\VehicleReportUseCaseHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VehicleReportController extends AbstractController
{
    private VehicleReportUseCaseHandler $handler;
    private VehicleReportValidator $validator;

    public function __construct(
        VehicleReportUseCaseHandler $handler,
        VehicleReportValidator $validator
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/reports/vehicles",
        operationId: "getVehicleReport",
        description: "Obtiene un reporte paginado de vehículos administrativos con filtros avanzados.",
        summary: "Reporte de vehículos administrativos.",
        security: [["bearerAuth" => []]],
        tags: ["Reportes - Vehículos"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "licensePlate", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "brandId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "modelId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "colorId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "manufactureYearFrom", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "manufactureYearTo", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "seatCount", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "passengerCount", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "fuelTypeId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "vehicleClassId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "categoryId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "active", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "companyId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "districtId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "statusId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "procedureTypeId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "modalityId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", default: "licensePlate"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Reporte paginado de vehículos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Reporte de vehículos obtenido exitosamente"),
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
                                    new OA\Property(property: "licensePlate", type: "string", example: "ABC-123"),
                                    new OA\Property(property: "brandName", type: "string", example: "Toyota"),
                                    new OA\Property(property: "modelName", type: "string", example: "Corolla"),
                                    new OA\Property(property: "colorName", type: "string", example: "Rojo"),
                                    new OA\Property(property: "manufactureYear", type: "integer", example: 2020),
                                    new OA\Property(property: "seatCount", type: "integer", example: 5),
                                    new OA\Property(property: "passengerCount", type: "integer", example: 5),
                                    new OA\Property(property: "fuelTypeName", type: "string", example: "Gasolina"),
                                    new OA\Property(property: "vehicleClassName", type: "string", example: "Sedán"),
                                    new OA\Property(property: "categoryName", type: "string", example: "Particular"),
                                    new OA\Property(property: "active", type: "boolean", example: true),
                                    new OA\Property(property: "companyName", type: "string", example: "Empresa S.A."),
                                    new OA\Property(property: "districtName", type: "string", example: "Lima"),
                                    new OA\Property(property: "statusName", type: "string", example: "Vigente"),
                                    new OA\Property(property: "procedureTypeName", type: "string", example: "Renovación"),
                                    new OA\Property(property: "modalityName", type: "string", example: "Taxi"),
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
            $dto = VehicleReportRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el reporte de vehículos: ' . $e->getMessage());
        }
    }
}

