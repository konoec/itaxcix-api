<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Infraction;

use InvalidArgumentException;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityListUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityCreateUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityUpdateUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityRequestDTO;
use itaxcix\Shared\Validators\useCases\InfractionSeverity\InfractionSeverityValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InfractionSeverityController extends AbstractController
{
    private InfractionSeverityListUseCaseHandler $listHandler;
    private InfractionSeverityCreateUseCaseHandler $createHandler;
    private InfractionSeverityUpdateUseCaseHandler $updateHandler;
    private InfractionSeverityDeleteUseCaseHandler $deleteHandler;
    private InfractionSeverityValidator $validator;

    public function __construct(
        InfractionSeverityListUseCaseHandler $listHandler,
        InfractionSeverityCreateUseCaseHandler $createHandler,
        InfractionSeverityUpdateUseCaseHandler $updateHandler,
        InfractionSeverityDeleteUseCaseHandler $deleteHandler,
        InfractionSeverityValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/infraction-severities",
        operationId: "getInfractionSeverities",
        description: "Obtiene gravedades de infracción con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista gravedades de infracción con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Severity"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de gravedad", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de gravedades de infracción con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Gravedades de infracción obtenidas exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Leve"),
                                    new OA\Property(property: "active", type: "boolean", example: true)
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "total_items", type: "integer", example: 25),
                                new OA\Property(property: "total_pages", type: "integer", example: 2),
                                new OA\Property(property: "has_next_page", type: "boolean", example: true),
                                new OA\Property(property: "has_previous_page", type: "boolean", example: false)
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
            $paginationRequest = InfractionSeverityPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener las gravedades de infracción: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/infraction-severities",
        operationId: "createInfractionSeverity",
        description: "Crea una nueva gravedad de infracción.",
        summary: "Crear nueva gravedad de infracción.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Severity"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Leve", description: "Nombre de la gravedad"),
                new OA\Property(property: "active", description: "Estado activo de la gravedad", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Gravedad de infracción creada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Gravedad de infracción creada exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Leve"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validationErrors = $this->validator->validateCreate($data);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $requestDTO = InfractionSeverityRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear la gravedad de infracción: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/infraction-severities/{id}",
        operationId: "updateInfractionSeverity",
        description: "Actualiza una gravedad de infracción existente.",
        summary: "Actualizar gravedad de infracción.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Severity"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la gravedad de infracción", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Leve Modificada", description: "Nombre de la gravedad"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo de la gravedad")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Gravedad de infracción actualizada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Gravedad de infracción actualizada exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Leve Modificada"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');
            $data = $this->getJsonBody($request);

            $idValidationErrors = $this->validator->validateId($id);
            if (!empty($idValidationErrors)) {
                return $this->validationError($idValidationErrors);
            }

            $validationErrors = $this->validator->validateUpdate($data, $id);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $requestDTO = InfractionSeverityRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar la gravedad de infracción: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/infraction-severities/{id}",
        operationId: "deleteInfractionSeverity",
        description: "Elimina una gravedad de infracción.",
        summary: "Eliminar gravedad de infracción.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Severity"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la gravedad de infracción", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Gravedad de infracción eliminada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Gravedad de infracción eliminada exitosamente")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');

            $validationErrors = $this->validator->validateId($id);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $result = $this->deleteHandler->handle($id);

            if (!$result) {
                return $this->error('No se pudo eliminar la gravedad de infracción', 400);
            }

            return $this->ok(
                [
                    'message' => 'Gravedad de infracción eliminada exitosamente'
                ]
            );
        } catch (\Exception $e) {
            return $this->error('Error al eliminar la gravedad de infracción: ' . $e->getMessage());
        }
    }
}

