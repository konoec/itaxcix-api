<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Audit;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogRequestDTO;
use itaxcix\Shared\Validators\useCases\AuditLog\AuditLogValidator;
use itaxcix\Core\Handler\AuditLog\AuditLogUseCaseHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuditController extends AbstractController
{
    private AuditLogUseCaseHandler $handler;
    private AuditLogValidator $validator;

    public function __construct(
        AuditLogUseCaseHandler $handler,
        AuditLogValidator $validator
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/audit",
        operationId: "listAuditLogs",
        description: "Lista registros de auditoría con filtros avanzados y paginación.",
        summary: "Listado de auditoría.",
        security: [["bearerAuth" => []]],
        tags: ["Auditoría"]
    )]
    #[OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "affectedTable", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "operation", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["INSERT", "UPDATE", "DELETE"]))]
    #[OA\Parameter(name: "systemUser", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "dateFrom", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date"))]
    #[OA\Parameter(name: "dateTo", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date"))]
    #[OA\Parameter(name: "sortBy", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["date", "affectedTable", "operation", "systemUser"], default: "date"))]
    #[OA\Parameter(name: "sortDirection", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "DESC"))]
    #[OA\Response(
        response: 200,
        description: "Listado paginado de auditoría",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Registros de auditoría obtenidos exitosamente"),
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
                                    new OA\Property(property: "affectedTable", type: "string", example: "tb_usuario"),
                                    new OA\Property(property: "operation", type: "string", example: "UPDATE"),
                                    new OA\Property(property: "systemUser", type: "string", example: "postgres"),
                                    new OA\Property(property: "date", type: "string", format: "date-time", example: "2025-06-29 10:00:00"),
                                    new OA\Property(property: "previousData", type: "object", nullable: true),
                                    new OA\Property(property: "newData", type: "object", nullable: true)
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
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $validationErrors = $this->validator->validateFilters($queryParams);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }
            $dto = AuditLogRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener los registros de auditoría: ' . $e->getMessage());
        }
    }

    #[OA\Get(
        path: "/audit/{id}",
        operationId: "getAuditLogDetails",
        description: "Obtiene el detalle de un registro de auditoría por ID.",
        summary: "Detalle de auditoría por ID.",
        security: [["bearerAuth" => []]],
        tags: ["Auditoría"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalle del registro de auditoría",
                content: new OA\JsonContent(ref: "#/components/schemas/AuditLogResponseDTO")
            ),
            new OA\Response(response: 404, description: "Registro de auditoría no encontrado")
        ]
    )]
    public function getDetails(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');
            $result = $this->handler->getDetails($id);
            if (!$result) {
                return $this->error('Registro de auditoría no encontrado', 404);
            }
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener el detalle de auditoría: ' . $e->getMessage());
        }
    }

    #[OA\Get(
        path: "/audit/user/{userId}",
        operationId: "getUserAuditLog",
        description: "Obtiene el log de auditoría filtrado por usuario de sistema.",
        summary: "Auditoría por usuario de sistema.",
        security: [["bearerAuth" => []]],
        tags: ["Auditoría"],
        parameters: [
            new OA\Parameter(name: "userId", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Listado paginado de auditoría por usuario",
                content: new OA\JsonContent(ref: "#/components/schemas/AuditLogPaginationResponseDTO")
            )
        ]
    )]
    public function getUserAuditLog(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = $request->getAttribute('userId');
            $queryParams = $request->getQueryParams();
            $queryParams['systemUser'] = $userId;
            $dto = AuditLogRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener la auditoría del usuario: ' . $e->getMessage());
        }
    }

    #[OA\Get(
        path: "/audit/entity/{entityType}",
        operationId: "getEntityAuditLog",
        description: "Obtiene el log de auditoría filtrado por entidad/tablas.",
        summary: "Auditoría por entidad/tablas.",
        security: [["bearerAuth" => []]],
        tags: ["Auditoría"],
        parameters: [
            new OA\Parameter(name: "entityType", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Listado paginado de auditoría por entidad",
                content: new OA\JsonContent(ref: "#/components/schemas/AuditLogPaginationResponseDTO")
            )
        ]
    )]
    public function getEntityAuditLog(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $entityType = $request->getAttribute('entityType');
            $queryParams = $request->getQueryParams();
            $queryParams['affectedTable'] = $entityType;
            $dto = AuditLogRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener la auditoría de la entidad: ' . $e->getMessage());
        }
    }

    #[OA\Get(
        path: "/audit/action/{action}",
        operationId: "getActionAuditLog",
        description: "Obtiene el log de auditoría filtrado por acción (INSERT, UPDATE, DELETE).",
        summary: "Auditoría por acción.",
        security: [["bearerAuth" => []]],
        tags: ["Auditoría"],
        parameters: [
            new OA\Parameter(name: "action", in: "path", required: true, schema: new OA\Schema(type: "string", enum: ["INSERT", "UPDATE", "DELETE"]))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Listado paginado de auditoría por acción",
                content: new OA\JsonContent(ref: "#/components/schemas/AuditLogPaginationResponseDTO")
            )
        ]
    )]
    public function getActionAuditLog(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $action = $request->getAttribute('action');
            $queryParams = $request->getQueryParams();
            $queryParams['operation'] = $action;
            $dto = AuditLogRequestDTO::fromArray($queryParams);
            $result = $this->handler->handle($dto);
            return $this->ok($result->toArray());
        } catch (\Exception $e) {
            return $this->error('Error al obtener la auditoría por acción: ' . $e->getMessage());
        }
    }

    #[OA\Get(
        path: "/audit/export",
        operationId: "exportAuditLog",
        description: "Exporta el log de auditoría filtrado en formato CSV.",
        summary: "Exportar log de auditoría.",
        security: [["bearerAuth" => []]],
        tags: ["Auditoría"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Archivo CSV exportado",
                content: new OA\MediaType(mediaType: "text/csv")
            )
        ]
    )]
    public function exportAuditLog(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $dto = AuditLogRequestDTO::fromArray($queryParams);
            $csv = $this->handler->export($dto);
            $filename = 'audit-log-' . date('Ymd_His') . '.csv';
            return $this->csv($csv, $filename);
        } catch (\Exception $e) {
            return $this->error('Error al exportar el log de auditoría: ' . $e->getMessage());
        }
    }
}
