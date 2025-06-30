<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Driver;

use InvalidArgumentException;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusCreateUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusDeleteUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusListUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusRequestDTO;
use itaxcix\Shared\Validators\useCases\DriverStatus\DriverStatusValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DriverStatusController extends AbstractController
{
    private DriverStatusListUseCase $listUseCase;
    private DriverStatusCreateUseCase $createUseCase;
    private DriverStatusUpdateUseCase $updateUseCase;
    private DriverStatusDeleteUseCase $deleteUseCase;

    public function __construct(
        DriverStatusListUseCase $listUseCase,
        DriverStatusCreateUseCase $createUseCase,
        DriverStatusUpdateUseCase $updateUseCase,
        DriverStatusDeleteUseCase $deleteUseCase
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
    }

    #[OA\Get(
        path: "/admin/driver-statuses",
        operationId: "getDriverStatuses",
        description: "Obtiene estados de conductores con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista estados de conductores con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Driver Status"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del estado", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc"))]
    #[OA\Parameter(name: "onlyActive", description: "Incluir solo activos", in: "query", required: false, schema: new OA\Schema(type: "boolean", default: false))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de estados de conductores con metadatos administrativos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/DriverStatusResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 8),
                        new OA\Property(property: "perPage", type: "integer", example: 15),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 1),
                        new OA\Property(property: "search", type: "string", example: "disponible"),
                        new OA\Property(property: "filters", type: "object"),
                        new OA\Property(property: "sortBy", type: "string", example: "name"),
                        new OA\Property(property: "sortDirection", type: "string", example: "asc")
                    ], type: "object"),
                    new OA\Property(property: "predefinedStatuses", type: "object", description: "Estados predefinidos del sistema organizados por categoría")
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
        $dto = new DriverStatusPaginationRequestDTO(
            page: $page,
            perPage: $perPage,
            search: $search,
            name: $name,
            active: $active,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            onlyActive: $onlyActive
        );

        // Validar parámetros de ordenamiento
        if (!$dto->isValidSortBy()) {
            return $this->error("Campo de ordenamiento inválido. Valores permitidos: id, name, active", 400);
        }

        if (!$dto->isValidSortDirection()) {
            return $this->error("Dirección de ordenamiento inválida. Valores permitidos: asc, desc", 400);
        }

        try {
            $result = $this->listUseCase->execute($dto);

            // Agregar estados predefinidos para el panel administrativo
            $result['predefinedStatuses'] = DriverStatusValidator::getPredefinedStatuses();

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error("Error al obtener estados de conductores: " . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/driver-statuses",
        operationId: "createDriverStatus",
        description: "Crea un nuevo estado de conductor para el panel administrativo.",
        summary: "Crea estado de conductor.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/DriverStatusRequestDTO")
        ),
        tags: ["Admin - Driver Status"]
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de conductor creado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de conductor creado correctamente."),
                new OA\Property(property: "data", ref: "#/components/schemas/DriverStatusResponseDTO")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new DriverStatusValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new DriverStatusRequestDTO(
                id: null,
                name: (string)$data['name'],
                active: $data['active'] ?? true
            );

            $result = $this->createUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/driver-statuses/{id}",
        operationId: "updateDriverStatus",
        description: "Actualiza un estado de conductor en el panel administrativo.",
        summary: "Actualiza estado de conductor.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/DriverStatusRequestDTO")
        ),
        tags: ["Admin - Driver Status"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del estado de conductor a actualizar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de conductor actualizado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de conductor actualizado correctamente."),
                new OA\Property(property: "data", ref: "#/components/schemas/DriverStatusResponseDTO")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $id = (int)$request->getAttribute('id');

            $validator = new DriverStatusValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new DriverStatusRequestDTO(
                id: $id,
                name: (string)$data['name'],
                active: $data['active'] ?? true
            );

            $result = $this->updateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/admin/driver-statuses/{id}",
        operationId: "deleteDriverStatus",
        description: "Elimina (desactiva) un estado de conductor en el panel administrativo.",
        summary: "Elimina estado de conductor.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Driver Status"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del estado de conductor a eliminar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de conductor eliminado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de conductor eliminado correctamente.")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int)$request->getAttribute('id');
            $result = $this->deleteUseCase->execute($id);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/admin/driver-statuses/predefined-statuses",
        operationId: "getPredefinedDriverStatuses",
        description: "Obtiene los estados de conductor predefinidos del sistema organizados por categoría.",
        summary: "Lista estados predefinidos para conductores.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Driver Status"]
    )]
    #[OA\Response(
        response: 200,
        description: "Estados predefinidos obtenidos correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", type: "object", description: "Estados organizados por categoría")
            ],
            type: "object"
        )
    )]
    public function getPredefinedStatuses(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $predefinedStatuses = DriverStatusValidator::getPredefinedStatuses();
            return $this->ok(['predefinedStatuses' => $predefinedStatuses]);
        } catch (\Exception $e) {
            return $this->error("Error al obtener estados predefinidos: " . $e->getMessage(), 500);
        }
    }
}
