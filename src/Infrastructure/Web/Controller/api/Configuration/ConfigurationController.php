<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Configuration;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Configuration\ConfigurationCreateUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationDeleteUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationListUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationRequestDTO;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationResponseDTO;
use itaxcix\Shared\Validators\useCases\Configuration\ConfigurationValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ConfigurationController extends AbstractController
{
    private ConfigurationListUseCase $listUseCase;
    private ConfigurationCreateUseCase $createUseCase;
    private ConfigurationUpdateUseCase $updateUseCase;
    private ConfigurationDeleteUseCase $deleteUseCase;

    public function __construct(
        ConfigurationListUseCase $listUseCase,
        ConfigurationCreateUseCase $createUseCase,
        ConfigurationUpdateUseCase $updateUseCase,
        ConfigurationDeleteUseCase $deleteUseCase
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
    }

    #[OA\Get(
        path: "/admin/configurations",
        operationId: "getConfigurations",
        description: "Obtiene configuraciones del sistema con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista configuraciones del sistema con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Configuration"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en clave y valor", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "key", description: "Filtro por clave de configuración", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "value", description: "Filtro por valor de configuración", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "key", "value", "active"], default: "key"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc"))]
    #[OA\Parameter(name: "onlyActive", description: "Incluir solo activos", in: "query", required: false, schema: new OA\Schema(type: "boolean", default: false))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de configuraciones con metadatos administrativos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/ConfigurationResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 25),
                        new OA\Property(property: "perPage", type: "integer", example: 15),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 2),
                        new OA\Property(property: "search", type: "string", example: "app"),
                        new OA\Property(property: "filters", type: "object"),
                        new OA\Property(property: "sortBy", type: "string", example: "key"),
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
        $key = isset($queryParams['key']) ? trim((string)$queryParams['key']) : null;
        $value = isset($queryParams['value']) ? trim((string)$queryParams['value']) : null;
        $active = isset($queryParams['active']) ? filter_var($queryParams['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
        $sortBy = (string)($queryParams['sortBy'] ?? 'key');
        $sortDirection = strtolower((string)($queryParams['sortDirection'] ?? 'asc'));
        $onlyActive = filter_var($queryParams['onlyActive'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Limpiar valores vacíos
        if ($search === '') $search = null;
        if ($key === '') $key = null;
        if ($value === '') $value = null;

        // Crear DTO
        $dto = new ConfigurationPaginationRequestDTO(
            page: $page,
            perPage: $perPage,
            search: $search,
            key: $key,
            value: $value,
            active: $active,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            onlyActive: $onlyActive
        );

        // Validar parámetros de ordenamiento
        if (!$dto->isValidSortBy()) {
            return $this->error("Campo de ordenamiento inválido. Valores permitidos: id, key, value, active", 400);
        }

        if (!$dto->isValidSortDirection()) {
            return $this->error("Dirección de ordenamiento inválida. Valores permitidos: asc, desc", 400);
        }

        try {
            $result = $this->listUseCase->execute($dto);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error("Error al obtener configuraciones: " . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/configurations",
        operationId: "createConfiguration",
        description: "Crea una nueva configuración del sistema para el panel administrativo.",
        summary: "Crea configuración del sistema.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ConfigurationRequestDTO")
        ),
        tags: ["Admin - Configuration"]
    )]
    #[OA\Response(
        response: 200,
        description: "Configuración creada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Configuración creada correctamente."),
                new OA\Property(property: "data", ref: ConfigurationResponseDTO::class)
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new ConfigurationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new ConfigurationRequestDTO(
                id: null,
                key: (string)$data['key'],
                value: (string)$data['value'],
                active: $data['active'] ?? true
            );

            $result = $this->createUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/configurations/{id}",
        operationId: "updateConfiguration",
        description: "Actualiza una configuración del sistema en el panel administrativo.",
        summary: "Actualiza configuración del sistema.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ConfigurationRequestDTO")
        ),
        tags: ["Admin - Configuration"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID de la configuración a actualizar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Configuración actualizada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Configuración actualizada correctamente."),
                new OA\Property(property: "data", ref: "#/components/schemas/ConfigurationResponseDTO")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $id = (int)$request->getAttribute('id');

            $validator = new ConfigurationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new ConfigurationRequestDTO(
                id: $id,
                key: (string)$data['key'],
                value: (string)$data['value'],
                active: $data['active'] ?? true
            );

            $result = $this->updateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/admin/configurations/{id}",
        operationId: "deleteConfiguration",
        description: "Elimina (desactiva) una configuración del sistema en el panel administrativo.",
        summary: "Elimina configuración del sistema.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Configuration"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID de la configuración a eliminar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Configuración eliminada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Configuración eliminada correctamente.")
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
}
