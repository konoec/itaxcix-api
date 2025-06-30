<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Model\ModelCreateUseCase;
use itaxcix\Core\UseCases\Model\ModelDeleteUseCase;
use itaxcix\Core\UseCases\Model\ModelListUseCase;
use itaxcix\Core\UseCases\Model\ModelUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Model\ModelPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Model\ModelRequestDTO;
use itaxcix\Shared\Validators\useCases\Model\ModelValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ModelController extends AbstractController
{
    private ModelListUseCase $listUseCase;
    private ModelCreateUseCase $createUseCase;
    private ModelUpdateUseCase $updateUseCase;
    private ModelDeleteUseCase $deleteUseCase;
    private ModelValidator $validator;

    public function __construct(
        ModelListUseCase $listUseCase,
        ModelCreateUseCase $createUseCase,
        ModelUpdateUseCase $updateUseCase,
        ModelDeleteUseCase $deleteUseCase,
        ModelValidator $validator
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/models",
        operationId: "getModels",
        description: "Obtiene modelos de vehículos con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista modelos de vehículos con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Models"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre del modelo", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del modelo", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "brandId", description: "Filtro por ID de marca", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortOrder", description: "Orden de clasificación", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de modelos obtenida exitosamente",
        content: new OA\JsonContent(
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "data" => new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        "data" => new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    "id" => new OA\Property(property: "id", type: "integer", example: 1),
                                    "name" => new OA\Property(property: "name", type: "string", example: "Civic"),
                                    "brandId" => new OA\Property(property: "brandId", type: "integer", nullable: true, example: 1),
                                    "brandName" => new OA\Property(property: "brandName", type: "string", nullable: true, example: "Honda"),
                                    "active" => new OA\Property(property: "active", type: "boolean", example: true)
                                ]
                            )
                        ),
                        "pagination" => new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                "page" => new OA\Property(property: "page", type: "integer", example: 1),
                                "perPage" => new OA\Property(property: "perPage", type: "integer", example: 15),
                                "total" => new OA\Property(property: "total", type: "integer", example: 25),
                                "totalPages" => new OA\Property(property: "totalPages", type: "integer", example: 2)
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
        $queryParams = $request->getQueryParams();

        // Validar parámetros de paginación
        $validationErrors = $this->validator->validatePagination($queryParams);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        $paginationDTO = new ModelPaginationRequestDTO(
            (int)($queryParams['page'] ?? 1),
            (int)($queryParams['perPage'] ?? 15),
            $queryParams['search'] ?? null,
            $queryParams['name'] ?? null,
            isset($queryParams['brandId']) ? (int)$queryParams['brandId'] : null,
            isset($queryParams['active']) ? filter_var($queryParams['active'], FILTER_VALIDATE_BOOLEAN) : null,
            $queryParams['sortBy'] ?? 'name',
            strtoupper($queryParams['sortOrder'] ?? 'ASC')
        );

        try {
            $result = $this->listUseCase->execute($paginationDTO);
            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener la lista de modelos: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/models",
        operationId: "createModel",
        description: "Crea un nuevo modelo de vehículo en el sistema.",
        summary: "Crear modelo de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Models"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                "name" => new OA\Property(property: "name", type: "string", example: "Civic", description: "Nombre del modelo"),
                "brandId" => new OA\Property(property: "brandId", type: "integer", nullable: true, example: 1, description: "ID de la marca"),
                "active" => new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del modelo")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Modelo creado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "data" => new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        "id" => new OA\Property(property: "id", type: "integer", example: 1),
                        "name" => new OA\Property(property: "name", type: "string", example: "Civic"),
                        "brandId" => new OA\Property(property: "brandId", type: "integer", nullable: true, example: 1),
                        "brandName" => new OA\Property(property: "brandName", type: "string", nullable: true, example: "Honda"),
                        "active" => new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonBody($request);

        // Validar datos de entrada
        $validationErrors = $this->validator->validateCreate($data);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        $requestDTO = new ModelRequestDTO(
            $data['name'],
            $data['brandId'] ?? null,
            $data['active'] ?? true
        );

        try {
            $result = $this->createUseCase->execute($requestDTO);
            return $this->ok($result->toArray(), 201);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el modelo: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/models/{id}",
        operationId: "updateModel",
        description: "Actualiza un modelo de vehículo existente.",
        summary: "Actualizar modelo de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Models"]
    )]
    #[OA\Parameter(name: "id", description: "ID del modelo", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                "name" => new OA\Property(property: "name", type: "string", example: "Civic Type R", description: "Nombre del modelo"),
                "brandId" => new OA\Property(property: "brandId", type: "integer", nullable: true, example: 1, description: "ID de la marca"),
                "active" => new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del modelo")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Modelo actualizado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "data" => new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        "id" => new OA\Property(property: "id", type: "integer", example: 1),
                        "name" => new OA\Property(property: "name", type: "string", example: "Civic Type R"),
                        "brandId" => new OA\Property(property: "brandId", type: "integer", nullable: true, example: 1),
                        "brandName" => new OA\Property(property: "brandName", type: "string", nullable: true, example: "Honda"),
                        "active" => new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');
        $data = $this->getJsonBody($request);

        // Validar datos de entrada
        $validationErrors = $this->validator->validateUpdate($data, $id);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        $requestDTO = new ModelRequestDTO(
            $data['name'],
            $data['brandId'] ?? null,
            $data['active'] ?? true
        );

        try {
            $result = $this->updateUseCase->execute($id, $requestDTO);
            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el modelo: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/models/{id}",
        operationId: "deleteModel",
        description: "Elimina un modelo de vehículo del sistema.",
        summary: "Eliminar modelo de vehículo.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Vehicle Models"]
    )]
    #[OA\Parameter(name: "id", description: "ID del modelo", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Modelo eliminado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "message" => new OA\Property(property: "message", type: "string", example: "Modelo eliminado exitosamente")
            ]
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');

        try {
            $result = $this->deleteUseCase->execute($id);
            if ($result) {
                return $this->ok(['message' => 'Modelo eliminado exitosamente']);
            } else {
                return $this->error('No se pudo eliminar el modelo', 400);
            }
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el modelo: ' . $e->getMessage());
        }
    }
}
