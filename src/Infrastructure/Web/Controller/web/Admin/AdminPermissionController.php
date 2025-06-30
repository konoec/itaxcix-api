<?php

namespace itaxcix\Infrastructure\Web\Controller\web\Admin;

use itaxcix\Core\UseCases\Admin\Permission\ListPermissionsUseCase;
use itaxcix\Core\UseCases\Admin\Permission\CreatePermissionUseCase;
use itaxcix\Core\UseCases\Admin\Permission\GetPermissionUseCase;
use itaxcix\Core\UseCases\Admin\Permission\UpdatePermissionUseCase;
use itaxcix\Core\UseCases\Admin\Permission\DeletePermissionUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\Admin\Permission\ListPermissionsRequestDTO;
use itaxcix\Shared\DTO\Admin\Permission\CreatePermissionRequestDTO;
use itaxcix\Shared\DTO\Admin\Permission\UpdatePermissionRequestDTO;
use itaxcix\Shared\Validators\Admin\Permission\ListPermissionsValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AdminPermissionController - Controlador para gestión de permisos
 *
 * Este controlador proporciona funcionalidades para consultar permisos del sistema:
 * - Listado paginado con filtros (búsqueda, tipo web, estado)
 * - CRUD completo de permisos
 * - Útil para interfaces que necesitan mostrar permisos disponibles
 * - Usado principalmente en formularios de asignación masiva de permisos
 *
 * Los permisos normalmente son gestionados a través de seeders automáticos
 * y se consultan para asignación a roles, no se crean/editan manualmente.
 *
 * @package itaxcix\Infrastructure\Web\Controller\web\Admin
 * @author Sistema de Administración iTaxCix
 */
class AdminPermissionController extends AbstractController
{
    private ListPermissionsUseCase $listPermissionsUseCase;
    private CreatePermissionUseCase $createPermissionUseCase;
    private UpdatePermissionUseCase $updatePermissionUseCase;
    private DeletePermissionUseCase $deletePermissionUseCase;
    private ListPermissionsValidator $listPermissionsValidator;

    public function __construct(
        ListPermissionsUseCase $listPermissionsUseCase,
        CreatePermissionUseCase $createPermissionUseCase,
        UpdatePermissionUseCase $updatePermissionUseCase,
        DeletePermissionUseCase $deletePermissionUseCase,
        ListPermissionsValidator $listPermissionsValidator
    ) {
        $this->listPermissionsUseCase = $listPermissionsUseCase;
        $this->createPermissionUseCase = $createPermissionUseCase;
        $this->updatePermissionUseCase = $updatePermissionUseCase;
        $this->deletePermissionUseCase = $deletePermissionUseCase;
        $this->listPermissionsValidator = $listPermissionsValidator;
    }

    /**
     * Lista permisos con paginación y filtros
     *
     * Endpoint: GET /api/v1/admin/permissions
     * Permisos: admin.permissions.list
     *
     * Parámetros de consulta:
     * - page (int): Página actual (default: 1)
     * - limit (int): Elementos por página (default: 20, max: 100)
     * - search (string): Búsqueda por nombre del permiso
     * - webOnly (bool): Filtrar solo permisos web
     * - activeOnly (bool): Filtrar solo permisos activos (default: true)
     *
     * Principalmente usado para:
     * - Llenar formularios de asignación de permisos a roles
     * - Mostrar permisos disponibles en interfaces administrativas
     * - Auditoría de permisos del sistema
     *
     * Respuesta exitosa (200):
     * {
     *   "success": true,
     *   "data": {
     *     "permissions": [
     *       {
     *         "id": 1,
     *         "name": "admin.roles.list",
     *         "active": true,
     *         "web": true
     *       }
     *     ],
     *     "total": 25,
     *     "page": 1,
     *     "limit": 20,
     *     "totalPages": 2
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP
     * @return ResponseInterface Respuesta con lista paginada de permisos
     */
    #[OA\Get(
        path: "/api/v1/permissions",
        operationId: "listPermissions",
        description: "Lista permisos del sistema con paginación y filtros avanzados. Principalmente usado para llenar formularios de asignación de permisos a roles.",
        summary: "Listar permisos con paginación",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Permisos"]
    )]
    #[OA\Parameter(
        name: "page",
        description: "Número de página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", minimum: 1, example: 1)
    )]
    #[OA\Parameter(
        name: "limit",
        description: "Elementos por página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, example: 20)
    )]
    #[OA\Parameter(
        name: "search",
        description: "Búsqueda por nombre del permiso",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", example: "admin")
    )]
    #[OA\Parameter(
        name: "webOnly",
        description: "Filtrar solo permisos web",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", example: true)
    )]
    #[OA\Parameter(
        name: "activeOnly",
        description: "Filtrar solo permisos activos",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", example: true)
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de permisos obtenida exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "permissions",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "CONFIGURACIÓN"),
                                    new OA\Property(property: "active", type: "boolean", example: true),
                                    new OA\Property(property: "web", type: "boolean", example: true)
                                ]
                            )
                        ),
                        new OA\Property(property: "total", type: "integer", example: 25),
                        new OA\Property(property: "page", type: "integer", example: 1),
                        new OA\Property(property: "limit", type: "integer", example: 20),
                        new OA\Property(property: "totalPages", type: "integer", example: 2)
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: "No autorizado - Token inválido o expirado"
    )]
    #[OA\Response(
        response: 403,
        description: "Acceso denegado - Sin permisos de CONFIGURACIÓN"
    )]
    public function listPermissions(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();

            $errors = $this->listPermissionsValidator->validate($queryParams);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $requestDTO = new ListPermissionsRequestDTO(
                page: (int)($queryParams['page'] ?? 1),
                limit: (int)($queryParams['limit'] ?? 20),
                search: $queryParams['search'] ?? null,
                webOnly: isset($queryParams['webOnly']) ? $this->parseBool($queryParams['webOnly']) : null,
                activeOnly: isset($queryParams['activeOnly']) ? $this->parseBool($queryParams['activeOnly']) : null
            );

            $response = $this->listPermissionsUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Crea un nuevo permiso
     *
     * Endpoint: POST /api/v1/admin/permissions
     * Permisos: admin.permissions.create
     *
     * Cuerpo de la petición:
     * {
     *   "name": "admin.roles.create",
     *   "active": true,
     *   "web": true,
     *   "description": "Permiso para crear roles"
     * }
     *
     * Respuesta exitosa (201):
     * {
     *   "success": true,
     *   "message": "Permiso creado exitosamente",
     *   "data": {
     *     "id": 1,
     *     "name": "admin.roles.create",
     *     "active": true,
     *     "web": true,
     *     "description": "Permiso para crear roles"
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP
     * @return ResponseInterface Respuesta con el permiso creado
     */
    #[OA\Post(
        path: "/api/v1/permissions",
        operationId: "createPermission",
        description: "Crea un nuevo permiso en el sistema. Normalmente los permisos se crean automáticamente mediante seeders.",
        summary: "Crear nuevo permiso",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "admin.reports.view", description: "Nombre del permiso"),
                    new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del permiso"),
                    new OA\Property(property: "web", type: "boolean", example: true, description: "Acceso web del permiso")
                ]
            )
        ),
        tags: ["Admin - Permisos"]
    )]
    #[OA\Response(
        response: 201,
        description: "Permiso creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Permiso creado correctamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 26),
                        new OA\Property(property: "name", type: "string", example: "admin.reports.view"),
                        new OA\Property(property: "active", type: "boolean", example: true),
                        new OA\Property(property: "web", type: "boolean", example: true)
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error de validación - Datos incorrectos"
    )]
    #[OA\Response(
        response: 409,
        description: "Conflicto - Permiso ya existe"
    )]
    public function createPermission(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = $request->getParsedBody();

            $requestDTO = new CreatePermissionRequestDTO(
                name: $body['name'],
                active: $body['active'],
                web: $body['web'],
                description: $body['description']
            );

            $response = $this->createPermissionUseCase->execute($requestDTO);
            return $this->created($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Actualiza un permiso existente
     *
     * Endpoint: PUT /api/v1/admin/permissions/{id}
     * Permisos: admin.permissions.update
     *
     * Cuerpo de la petición:
     * {
     *   "name": "admin.roles.create",
     *   "active": true,
     *   "web": true,
     *   "description": "Permiso para crear roles - Actualizado"
     * }
     *
     * Respuesta exitosa (200):
     * {
     *   "success": true,
     *   "message": "Permiso actualizado exitosamente",
     *   "data": {
     *     "id": 1,
     *     "name": "admin.roles.create",
     *     "active": true,
     *     "web": true
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP
     * @return ResponseInterface Respuesta con el permiso actualizado
     */
    #[OA\Put(
        path: "/api/v1/permissions/{id}",
        operationId: "updatePermission",
        description: "Actualiza un permiso existente en el sistema.",
        summary: "Actualizar permiso",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", description: "Nombre del permiso", type: "string", example: "admin.reports.advanced"),
                    new OA\Property(property: "active", description: "Estado activo del permiso", type: "boolean", example: true),
                    new OA\Property(property: "web", description: "Acceso web del permiso", type: "boolean", example: false)
                ]
            )
        ),
        tags: ["Admin - Permisos"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del permiso",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Permiso actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Permiso actualizado correctamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "admin.reports.advanced"),
                        new OA\Property(property: "active", type: "boolean", example: true),
                        new OA\Property(property: "web", type: "boolean", example: false)
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Permiso no encontrado"
    )]
    public function updatePermission(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $data['id'] = $request->getAttribute('id');

            $id = (int)$data['id'];
            $body['name'] = $data['name'] ?? null;
            $body['active'] = $data['active'] ?? true;
            $body['web'] = $data['web'] ?? false;

            if ($id <= 0) {
                return $this->validationError(['id' => 'El ID del permiso debe ser un número positivo.']);
            }

            $requestDTO = new UpdatePermissionRequestDTO(
                id: $id,
                name: $body['name'],
                active: $body['active'],
                web: $body['web']
            );

            $response = $this->updatePermissionUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Elimina un permiso existente
     *
     * Endpoint: DELETE /api/v1/admin/permissions/{id}
     * Permisos: admin.permissions.delete
     *
     * Respuesta exitosa (204):
     * {
     *   "success": true,
     *   "message": "Permiso eliminado exitosamente"
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP
     * @return ResponseInterface Respuesta con el resultado de la eliminación
     */
    #[OA\Delete(
        path: "/api/v1/permissions/{id}",
        operationId: "deletePermission",
        description: "Elimina un permiso del sistema (desactivación lógica). Cuidado: esto puede afectar roles que tengan este permiso asignado.",
        summary: "Eliminar permiso",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Permisos"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del permiso",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Permiso eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Permiso eliminado correctamente")
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Permiso no encontrado"
    )]
    #[OA\Response(
        response: 409,
        description: "Conflicto - Permiso en uso por roles"
    )]
    public function deletePermission(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonBody($request);
        $data['id'] = $request->getAttribute('id');

        try {
            $this->deletePermissionUseCase->execute((int) $data['id']);
            return $this->noContent();

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Convierte un string 'true'/'false'/'1'/'0' a booleano real o null
     */
    private function parseBool($value): ?bool {
        if ($value === null) return null;
        if (is_bool($value)) return $value;
        if (is_string($value)) {
            $v = strtolower($value);
            if ($v === 'true' || $v === '1') return true;
            if ($v === 'false' || $v === '0') return false;
        }
        return (bool)$value;
    }
}
