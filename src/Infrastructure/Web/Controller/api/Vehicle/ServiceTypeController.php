<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\ServiceType\ServiceTypeListUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeRequestDTO;
use itaxcix\Shared\Validators\useCases\ServiceType\ServiceTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceTypeController extends AbstractController
{
    private ServiceTypeListUseCaseHandler $listHandler;
    private ServiceTypeCreateUseCaseHandler $createHandler;
    private ServiceTypeUpdateUseCaseHandler $updateHandler;
    private ServiceTypeDeleteUseCaseHandler $deleteHandler;
    private ServiceTypeValidator $validator;

    public function __construct(
        ServiceTypeListUseCaseHandler $listHandler,
        ServiceTypeCreateUseCaseHandler $createHandler,
        ServiceTypeUpdateUseCaseHandler $updateHandler,
        ServiceTypeDeleteUseCaseHandler $deleteHandler,
        ServiceTypeValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/service-types",
        operationId: "getServiceTypes",
        description: "Obtiene tipos de servicio con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista tipos de servicio con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Service Type"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del tipo de servicio", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de tipos de servicio con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipos de servicio obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Taxi"),
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
            $paginationRequest = ServiceTypePaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los tipos de servicio: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/service-types",
        operationId: "createServiceType",
        description: "Crea un nuevo tipo de servicio.",
        summary: "Crear nuevo tipo de servicio.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Service Type"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Taxi", description: "Nombre del tipo de servicio"),
                new OA\Property(property: "active", description: "Estado activo del tipo de servicio", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Tipo de servicio creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de servicio creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Taxi"),
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

            $requestDTO = ServiceTypeRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el tipo de servicio: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/service-types/{id}",
        operationId: "updateServiceType",
        description: "Actualiza un tipo de servicio existente.",
        summary: "Actualizar tipo de servicio.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Service Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de servicio", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Taxi Modificado", description: "Nombre del tipo de servicio"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de servicio")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de servicio actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de servicio actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Taxi Modificado"),
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

            $requestDTO = ServiceTypeRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el tipo de servicio: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/service-types/{id}",
        operationId: "deleteServiceType",
        description: "Elimina un tipo de servicio.",
        summary: "Eliminar tipo de servicio.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Service Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de servicio", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Tipo de servicio eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de servicio eliminado exitosamente")
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
                return $this->error('No se pudo eliminar el tipo de servicio. Verifique si tiene rutas activas relacionadas.', 400);
            }

            return $this->ok('Tipo de servicio eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el tipo de servicio: ' . $e->getMessage());
        }
    }
}
