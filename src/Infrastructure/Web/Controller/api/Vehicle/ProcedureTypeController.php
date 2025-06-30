<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeListUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeRequestDTO;
use itaxcix\Shared\Validators\useCases\ProcedureType\ProcedureTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProcedureTypeController extends AbstractController
{
    private ProcedureTypeListUseCaseHandler $listHandler;
    private ProcedureTypeCreateUseCaseHandler $createHandler;
    private ProcedureTypeUpdateUseCaseHandler $updateHandler;
    private ProcedureTypeDeleteUseCaseHandler $deleteHandler;
    private ProcedureTypeValidator $validator;

    public function __construct(
        ProcedureTypeListUseCaseHandler $listHandler,
        ProcedureTypeCreateUseCaseHandler $createHandler,
        ProcedureTypeUpdateUseCaseHandler $updateHandler,
        ProcedureTypeDeleteUseCaseHandler $deleteHandler,
        ProcedureTypeValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/procedure-types",
        operationId: "getProcedureTypes",
        description: "Obtiene tipos de trámite con búsqueda, filtros y paginación avanzada.",
        summary: "Lista tipos de trámite.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Procedure Type"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de tipo de trámite", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de tipos de trámite con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipos de trámite obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Licencia"),
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
            $paginationRequest = ProcedureTypePaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok('Tipos de trámite obtenidos exitosamente', $result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los tipos de trámite: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/procedure-types",
        operationId: "createProcedureType",
        description: "Crea un nuevo tipo de trámite.",
        summary: "Crear nuevo tipo de trámite.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Procedure Type"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Licencia", description: "Nombre del tipo de trámite"),
                new OA\Property(property: "active", description: "Estado activo del tipo de trámite", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Tipo de trámite creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de trámite creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Licencia"),
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

            $requestDTO = ProcedureTypeRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok('Tipo de trámite creado exitosamente', $result->toArray(), 201);
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el tipo de trámite: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/procedure-types/{id}",
        operationId: "updateProcedureType",
        description: "Actualiza un tipo de trámite existente.",
        summary: "Actualizar tipo de trámite.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Procedure Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de trámite", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Licencia modificada", description: "Nombre del tipo de trámite"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de trámite")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de trámite actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de trámite actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Licencia modificada"),
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

            $requestDTO = ProcedureTypeRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok('Tipo de trámite actualizado exitosamente', $result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el tipo de trámite: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/procedure-types/{id}",
        operationId: "deleteProcedureType",
        description: "Elimina un tipo de trámite.",
        summary: "Eliminar tipo de trámite.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Procedure Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de trámite", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Tipo de trámite eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de trámite eliminado exitosamente")
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
                return $this->error('No se pudo eliminar el tipo de trámite', 400);
            }

            return $this->ok('Tipo de trámite eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el tipo de trámite: ' . $e->getMessage());
        }
    }
}

