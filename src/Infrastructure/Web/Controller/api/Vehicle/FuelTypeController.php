<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\FuelType\FuelTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\FuelType\FuelTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\FuelType\FuelTypeListUseCaseHandler;
use itaxcix\Core\Handler\FuelType\FuelTypeUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeRequestDTO;
use itaxcix\Shared\Validators\useCases\FuelType\FuelTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FuelTypeController extends AbstractController
{
    private FuelTypeListUseCaseHandler $listHandler;
    private FuelTypeCreateUseCaseHandler $createHandler;
    private FuelTypeUpdateUseCaseHandler $updateHandler;
    private FuelTypeDeleteUseCaseHandler $deleteHandler;
    private FuelTypeValidator $validator;

    public function __construct(
        FuelTypeListUseCaseHandler $listHandler,
        FuelTypeCreateUseCaseHandler $createHandler,
        FuelTypeUpdateUseCaseHandler $updateHandler,
        FuelTypeDeleteUseCaseHandler $deleteHandler,
        FuelTypeValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/fuel-types",
        operationId: "getFuelTypes",
        description: "Obtiene tipos de combustible con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista tipos de combustible con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - FuelType"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del tipo de combustible", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de tipos de combustible con paginación",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipos de combustible obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Gasolina 95"),
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
            ]
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $paginationRequest = FuelTypePaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok([
                'data' => $result,
                'message' => 'Tipos de combustible obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->error('Error al obtener tipos de combustible: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/fuel-types",
        operationId: "createFuelType",
        description: "Crea un nuevo tipo de combustible en el sistema.",
        summary: "Crear nuevo tipo de combustible.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - FuelType"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Gasolina 98", description: "Nombre del tipo de combustible"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de combustible")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Tipo de combustible creado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de combustible creado exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Gasolina 98"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = $this->getJsonBody($request);
            $fuelTypeRequest = FuelTypeRequestDTO::fromArray($body);

            $errors = $this->validator->validate($fuelTypeRequest);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->createHandler->handle($fuelTypeRequest);

            return $this->ok([
                'data' => $result->toArray(),
                'message' => 'Tipo de combustible creado exitosamente'
            ], 201);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear tipo de combustible: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/fuel-types/{id}",
        operationId: "updateFuelType",
        description: "Actualiza un tipo de combustible existente.",
        summary: "Actualizar tipo de combustible.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - FuelType"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de combustible", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Gasolina Premium Actualizada", description: "Nombre del tipo de combustible"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de combustible")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de combustible actualizado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de combustible actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Gasolina Premium Actualizada"),
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
            $body = $this->getJsonBody($request);
            $fuelTypeRequest = FuelTypeRequestDTO::fromArray($body);

            $errors = $this->validator->validateForUpdate($fuelTypeRequest, $id);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->updateHandler->handle($id, $fuelTypeRequest);

            return $this->ok([
                'data' => $result->toArray(),
                'message' => 'Tipo de combustible actualizado exitosamente'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar tipo de combustible: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/fuel-types/{id}",
        operationId: "deleteFuelType",
        description: "Elimina un tipo de combustible del sistema.",
        summary: "Eliminar tipo de combustible.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - FuelType"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de combustible", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Tipo de combustible eliminado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de combustible eliminado exitosamente"),
                new OA\Property(property: "data", type: "object", example: null)
            ]
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');

            $result = $this->deleteHandler->handle($id);

            if (!$result) {
                return $this->error('No se pudo eliminar el tipo de combustible', 500);
            }

            return $this->ok([
                'data' => null,
                'message' => 'Tipo de combustible eliminado exitosamente'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al eliminar tipo de combustible: ' . $e->getMessage(), 500);
        }
    }
}
