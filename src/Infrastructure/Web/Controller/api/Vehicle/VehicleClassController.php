<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\VehicleClass\VehicleClassCreateUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassDeleteUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassListUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassRequestDTO;
use itaxcix\Shared\Validators\useCases\VehicleClass\VehicleClassValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VehicleClassController extends AbstractController
{
    private VehicleClassListUseCaseHandler $listHandler;
    private VehicleClassCreateUseCaseHandler $createHandler;
    private VehicleClassUpdateUseCaseHandler $updateHandler;
    private VehicleClassDeleteUseCaseHandler $deleteHandler;
    private VehicleClassValidator $validator;

    public function __construct(
        VehicleClassListUseCaseHandler $listHandler,
        VehicleClassCreateUseCaseHandler $createHandler,
        VehicleClassUpdateUseCaseHandler $updateHandler,
        VehicleClassDeleteUseCaseHandler $deleteHandler,
        VehicleClassValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/vehicle-classes",
        operationId: "getVehicleClasses",
        description: "Obtiene clases de vehículos con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista clases de vehículos con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Class"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de la clase", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de clases de vehículos con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Clases de vehículos obtenidas exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Compacto"),
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
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $paginationRequest = VehicleClassPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener las clases de vehículos: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/vehicle-classes",
        operationId: "createVehicleClass",
        description: "Crea una nueva clase de vehículo.",
        summary: "Crear nueva clase de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Class"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "SUV", description: "Nombre de la clase de vehículo"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo de la clase")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Clase de vehículo creada exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Clase de vehículo creada exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "SUV"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
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

            $requestDTO = VehicleClassRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok($result->toArray(), 201);
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear la clase de vehículo: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/vehicle-classes/{id}",
        operationId: "updateVehicleClass",
        description: "Actualiza una clase de vehículo existente.",
        summary: "Actualizar clase de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Class"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la clase de vehículo", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "SUV Modificado", description: "Nombre de la clase de vehículo"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo de la clase")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Clase de vehículo actualizada exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Clase de vehículo actualizada exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "SUV Modificado"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
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

            $requestDTO = VehicleClassRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar la clase de vehículo: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/vehicle-classes/{id}",
        operationId: "deleteVehicleClass",
        description: "Elimina una clase de vehículo.",
        summary: "Eliminar clase de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Class"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la clase de vehículo", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Clase de vehículo eliminada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Clase de vehículo eliminada exitosamente")
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
                return $this->error('No se pudo eliminar la clase de vehículo', 400);
            }

            return $this->ok('Clase de vehículo eliminada exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar la clase de vehículo: ' . $e->getMessage());
        }
    }
}
