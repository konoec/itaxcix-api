<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Incident;

use InvalidArgumentException;
use itaxcix\Core\Handler\IncidentType\IncidentTypeListUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeRequestDTO;
use itaxcix\Shared\Validators\useCases\IncidentType\IncidentTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IncidentTypeController extends AbstractController
{
    private IncidentTypeListUseCaseHandler $listHandler;
    private IncidentTypeCreateUseCaseHandler $createHandler;
    private IncidentTypeUpdateUseCaseHandler $updateHandler;
    private IncidentTypeDeleteUseCaseHandler $deleteHandler;
    private IncidentTypeValidator $validator;

    public function __construct(
        IncidentTypeListUseCaseHandler $listHandler,
        IncidentTypeCreateUseCaseHandler $createHandler,
        IncidentTypeUpdateUseCaseHandler $updateHandler,
        IncidentTypeDeleteUseCaseHandler $deleteHandler,
        IncidentTypeValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/incident-types",
        operationId: "getIncidentTypes",
        description: "Obtiene tipos de incidencia con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista tipos de incidencia con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Incident Type"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del tipo de incidencia", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de tipos de incidencia con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipos de incidencia obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Accidente"),
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
            $paginationRequest = IncidentTypePaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los tipos de incidencia: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/incident-types",
        operationId: "createIncidentType",
        description: "Crea un nuevo tipo de incidencia.",
        summary: "Crear nuevo tipo de incidencia.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Incident Type"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Accidente", description: "Nombre del tipo de incidencia"),
                new OA\Property(property: "active", description: "Estado activo del tipo de incidencia", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Tipo de incidencia creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de incidencia creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Accidente"),
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

            $requestDTO = IncidentTypeRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok('Tipo de incidencia creado exitosamente', $result->toArray(), 201);
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el tipo de incidencia: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/incident-types/{id}",
        operationId: "updateIncidentType",
        description: "Actualiza un tipo de incidencia existente.",
        summary: "Actualizar tipo de incidencia.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Incident Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de incidencia", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Accidente Modificado", description: "Nombre del tipo de incidencia"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de incidencia")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de incidencia actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de incidencia actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Accidente Modificado"),
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

            $requestDTO = IncidentTypeRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok('Tipo de incidencia actualizado exitosamente', $result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el tipo de incidencia: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/incident-types/{id}",
        operationId: "deleteIncidentType",
        description: "Elimina un tipo de incidencia.",
        summary: "Eliminar tipo de incidencia.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Incident Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de incidencia", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Tipo de incidencia eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de incidencia eliminado exitosamente")
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
                return $this->error('No se pudo eliminar el tipo de incidencia', 400);
            }

            return $this->ok('Tipo de incidencia eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el tipo de incidencia: ' . $e->getMessage());
        }
    }
}

