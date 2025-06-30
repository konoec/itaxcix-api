<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\Category\CategoryCreateUseCaseHandler;
use itaxcix\Core\Handler\Category\CategoryDeleteUseCaseHandler;
use itaxcix\Core\Handler\Category\CategoryListUseCaseHandler;
use itaxcix\Core\Handler\Category\CategoryUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Category\CategoryPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Category\CategoryRequestDTO;
use itaxcix\Shared\Validators\useCases\Category\CategoryValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController extends AbstractController
{
    private CategoryListUseCaseHandler $listHandler;
    private CategoryCreateUseCaseHandler $createHandler;
    private CategoryUpdateUseCaseHandler $updateHandler;
    private CategoryDeleteUseCaseHandler $deleteHandler;
    private CategoryValidator $validator;

    public function __construct(
        CategoryListUseCaseHandler $listHandler,
        CategoryCreateUseCaseHandler $createHandler,
        CategoryUpdateUseCaseHandler $updateHandler,
        CategoryDeleteUseCaseHandler $deleteHandler,
        CategoryValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/categories",
        operationId: "getVehicleCategories",
        description: "Obtiene categorías de vehículos con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista categorías de vehículos con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Category"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de la categoría", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de categorías de vehículos con paginación",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Categorías de vehículos obtenidas exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Automóvil"),
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
            $paginationRequest = CategoryPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok([
                'data' => $result,
                'message' => 'Categorías de vehículos obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->error('Error al obtener categorías de vehículos: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/categories",
        operationId: "createVehicleCategory",
        description: "Crea una nueva categoría de vehículo en el sistema.",
        summary: "Crear nueva categoría de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Category"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Motocicleta", description: "Nombre de la categoría de vehículo"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo de la categoría")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Categoría de vehículo creada exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Categoría de vehículo creada exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Motocicleta"),
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
            $categoryRequest = CategoryRequestDTO::fromArray($body);

            $errors = $this->validator->validate($categoryRequest);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->createHandler->handle($categoryRequest);

            return $this->ok([
                'data' => $result->toArray(),
                'message' => 'Categoría de vehículo creada exitosamente'
            ], 201);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear categoría de vehículo: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/categories/{id}",
        operationId: "updateVehicleCategory",
        description: "Actualiza una categoría de vehículo existente.",
        summary: "Actualizar categoría de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Category"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la categoría de vehículo", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Automóvil Actualizado", description: "Nombre de la categoría de vehículo"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo de la categoría")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Categoría de vehículo actualizada exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Categoría de vehículo actualizada exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Automóvil Actualizado"),
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
            $categoryRequest = CategoryRequestDTO::fromArray($body);

            $errors = $this->validator->validateForUpdate($categoryRequest, $id);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->updateHandler->handle($id, $categoryRequest);

            return $this->ok([
                'data' => $result->toArray(),
                'message' => 'Categoría de vehículo actualizada exitosamente'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar categoría de vehículo: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/categories/{id}",
        operationId: "deleteVehicleCategory",
        description: "Elimina una categoría de vehículo del sistema.",
        summary: "Eliminar categoría de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Category"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la categoría de vehículo", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Categoría de vehículo eliminada exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Categoría de vehículo eliminada exitosamente"),
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
                return $this->error('No se pudo eliminar la categoría de vehículo', 500);
            }

            return $this->ok([
                'data' => null,
                'message' => 'Categoría de vehículo eliminada exitosamente'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al eliminar categoría de vehículo: ' . $e->getMessage(), 500);
        }
    }
}
