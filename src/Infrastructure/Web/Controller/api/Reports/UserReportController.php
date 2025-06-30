<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Reports;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportRequestDTO;
use itaxcix\Shared\Validators\useCases\UserReport\UserReportValidator;
use itaxcix\Core\Handler\UserReport\UserReportUseCaseHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserReportController extends AbstractController
{
    private UserReportUseCaseHandler $handler;
    private UserReportValidator $validator;

    public function __construct(
        UserReportUseCaseHandler $handler,
        UserReportValidator $validator
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/reports/users",
        operationId: "getUserReport",
        description: "Obtiene un reporte paginado de usuarios administrativos con filtros avanzados.",
        summary: "Reporte de usuarios administrativos.",
        security: [["bearerAuth" => []]],
        tags: ["Reportes - Usuarios"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "name", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "lastName", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "document", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "documentTypeId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "statusId", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "email", in: "query", required: false, schema: new OA\Schema(type: "string", format: "email"))]
    #[OA\Parameter(name: "phone", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "validationStartDate", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01"))]
    #[OA\Parameter(name: "validationEndDate", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["name", "lastName", "document", "email", "phone", "validationDate"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Reporte paginado de usuarios",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Reporte de usuarios obtenido exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Juan"),
                                    new OA\Property(property: "lastName", type: "string", example: "Pérez"),
                                    new OA\Property(property: "document", type: "string", example: "12345678"),
                                    new OA\Property(property: "documentType", type: "string", example: "DNI"),
                                    new OA\Property(property: "status", type: "string", example: "Activo"),
                                    new OA\Property(property: "email", type: "string", example: "juan.perez@email.com"),
                                    new OA\Property(property: "phone", type: "string", example: "+51999999999"),
                                    new OA\Property(property: "active", type: "boolean", example: true),
                                    new OA\Property(property: "validationDate", type: "string", format: "date-time", example: "2024-06-01 08:00:00")
                                ]
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
            $dto = UserReportRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el reporte de usuarios: ' . $e->getMessage());
        }
    }
}

