<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\Brand\BrandCreateUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandDeleteUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandListUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Brand\BrandPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Brand\BrandRequestDTO;
use itaxcix\Shared\Validators\useCases\Brand\BrandValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BrandController extends AbstractController
{
    private BrandListUseCaseHandler $listHandler;
    private BrandCreateUseCaseHandler $createHandler;
    private BrandUpdateUseCaseHandler $updateHandler;
    private BrandDeleteUseCaseHandler $deleteHandler;
    private BrandValidator $validator;

    public function __construct(
        BrandListUseCaseHandler $listHandler,
        BrandCreateUseCaseHandler $createHandler,
        BrandUpdateUseCaseHandler $updateHandler,
        BrandDeleteUseCaseHandler $deleteHandler,
        BrandValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/brands",
        operationId: "getBrands",
        description: "Obtiene marcas de vehículos con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista marcas de vehículos con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Brand"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre de marca", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de marca", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc"))]
    #[OA\Parameter(name: "onlyActive", description: "Incluir solo activos", in: "query", required: false, schema: new OA\Schema(type: "boolean", default: false))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de marcas con metadatos administrativos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/BrandResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 25),
                        new OA\Property(property: "perPage", type: "integer", example: 15),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 2),
                        new OA\Property(property: "search", type: "string", example: "Toyota"),
                        new OA\Property(property: "filters", type: "object"),
                        new OA\Property(property: "sortBy", type: "string", example: "name"),
                        new OA\Property(property: "sortDirection", type: "string", example: "asc")
                    ], type: "object")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        // Extraer y validar parámetros
        $page = max(1, (int)($queryParams['page'] ?? 1));
        $perPage = max(1, min(100, (int)($queryParams['perPage'] ?? 15))); // Default 15 para admin
        $search = isset($queryParams['search']) ? trim((string)$queryParams['search']) : null;
        $name = isset($queryParams['name']) ? trim((string)$queryParams['name']) : null;
        $active = isset($queryParams['active']) ? filter_var($queryParams['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
        $sortBy = (string)($queryParams['sortBy'] ?? 'name');
        $sortDirection = strtolower((string)($queryParams['sortDirection'] ?? 'asc'));
        $onlyActive = filter_var($queryParams['onlyActive'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Limpiar valores vacíos
        if ($search === '') $search = null;
        if ($name === '') $name = null;

        // Crear DTO
        $dto = new BrandPaginationRequestDTO(
            page: $page,
            perPage: $perPage,
            search: $search,
            name: $name,
            active: $active,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            onlyActive: $onlyActive
        );

        $result = $this->listHandler->handle($dto);
        return $this->ok($result);
    }

    #[OA\Post(
        path: "/admin/brands",
        operationId: "createBrand",
        description: "Crea una nueva marca de vehículo.",
        summary: "Crear marca de vehículo",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Brand"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/BrandRequestDTO")
    )]
    #[OA\Response(
        response: 201,
        description: "Marca creada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Marca creada exitosamente"),
                new OA\Property(property: "data", ref: "#/components/schemas/BrandResponseDTO")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Errores de validación"),
                new OA\Property(property: "data", type: "object")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonBody($request);

        $dto = new BrandRequestDTO(
            name: $data['name'] ?? '',
            active: $data['active'] ?? true
        );

        $errors = $this->validator->validateForCreate($dto);
        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        try {
            $result = $this->createHandler->handle($dto);
            return $this->ok($result, 'Marca creada exitosamente', 201);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/brands/{id}",
        operationId: "updateBrand",
        description: "Actualiza una marca de vehículo existente.",
        summary: "Actualizar marca de vehículo",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Brand"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID de la marca",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/BrandRequestDTO")
    )]
    #[OA\Response(
        response: 200,
        description: "Marca actualizada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Marca actualizada exitosamente"),
                new OA\Property(property: "data", ref: "#/components/schemas/BrandResponseDTO")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $this->getJsonBody($request);

        $dto = new BrandRequestDTO(
            name: $data['name'] ?? '',
            active: $data['active'] ?? true
        );

        $errors = $this->validator->validateForUpdate($dto, $id);
        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        try {
            $result = $this->updateHandler->handle($id, $dto);
            return $this->ok($result, 'Marca actualizada exitosamente');
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    #[OA\Delete(
        path: "/admin/brands/{id}",
        operationId: "deleteBrand",
        description: "Elimina una marca de vehículo.",
        summary: "Eliminar marca de vehículo",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Brand"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID de la marca",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Marca eliminada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Marca eliminada exitosamente"),
                new OA\Property(property: "data", type: "null")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        try {
            $this->deleteHandler->handle($id);
            return $this->ok(null, 'Marca eliminada exitosamente');
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }
}
