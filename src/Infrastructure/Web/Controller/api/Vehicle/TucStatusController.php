<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\TucStatus\TucStatusListUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusRequestDTO;
use itaxcix\Shared\Validators\useCases\TucStatus\TucStatusValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TucStatusController extends AbstractController
{
    private TucStatusListUseCaseHandler $listHandler;
    private TucStatusCreateUseCaseHandler $createHandler;
    private TucStatusUpdateUseCaseHandler $updateHandler;
    private TucStatusDeleteUseCaseHandler $deleteHandler;
    private TucStatusValidator $validator;

    public function __construct(
        TucStatusListUseCaseHandler $listHandler,
        TucStatusCreateUseCaseHandler $createHandler,
        TucStatusUpdateUseCaseHandler $updateHandler,
        TucStatusDeleteUseCaseHandler $deleteHandler,
        TucStatusValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/tuc-statuses",
        operationId: "getTucStatuses",
        description: "Obtiene estados TUC con búsqueda, filtros y paginación avanzada.",
        summary: "Lista estados TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Status"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de estado", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de estados TUC con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estados TUC obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Activo"),
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
            $paginationRequest = TucStatusPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok('Estados TUC obtenidos exitosamente', $result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los estados TUC: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/tuc-statuses",
        operationId: "createTucStatus",
        description: "Crea un nuevo estado TUC.",
        summary: "Crear nuevo estado TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Status"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Activo", description: "Nombre del estado TUC"),
                new OA\Property(property: "active", description: "Estado activo del estado TUC", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Estado TUC creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado TUC creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Activo"),
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

            $requestDTO = TucStatusRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok('Estado TUC creado exitosamente', $result->toArray(), 201);
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el estado TUC: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/tuc-statuses/{id}",
        operationId: "updateTucStatus",
        description: "Actualiza un estado TUC existente.",
        summary: "Actualizar estado TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado TUC", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Activo modificado", description: "Nombre del estado TUC"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del estado TUC")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Estado TUC actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado TUC actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Activo modificado"),
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

            $requestDTO = TucStatusRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok('Estado TUC actualizado exitosamente', $result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el estado TUC: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/tuc-statuses/{id}",
        operationId: "deleteTucStatus",
        description: "Elimina un estado TUC.",
        summary: "Eliminar estado TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado TUC", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Estado TUC eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado TUC eliminado exitosamente")
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
                return $this->error('No se pudo eliminar el estado TUC', 400);
            }

            return $this->ok('Estado TUC eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el estado TUC: ' . $e->getMessage());
        }
    }
}

