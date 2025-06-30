<?php

namespace itaxcix\Infrastructure\Web\Controller\web\Admin;

use Exception;
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
use itaxcix\Shared\Validators\useCases\Admin\PermissionCreateRequestValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminPermissionController extends AbstractController
{
    private ListPermissionsUseCase $listPermissionsUseCase;
    private CreatePermissionUseCase $createPermissionUseCase;
    private UpdatePermissionUseCase $updatePermissionUseCase;
    private DeletePermissionUseCase $deletePermissionUseCase;
    private ListPermissionsValidator $listPermissionsValidator;
    private PermissionCreateRequestValidator $permissionCreateRequestValidator;

    public function __construct(
        ListPermissionsUseCase $listPermissionsUseCase,
        CreatePermissionUseCase $createPermissionUseCase,
        UpdatePermissionUseCase $updatePermissionUseCase,
        DeletePermissionUseCase $deletePermissionUseCase,
        ListPermissionsValidator $listPermissionsValidator,
        PermissionCreateRequestValidator $permissionCreateRequestValidator
    ) {
        $this->listPermissionsUseCase = $listPermissionsUseCase;
        $this->createPermissionUseCase = $createPermissionUseCase;
        $this->updatePermissionUseCase = $updatePermissionUseCase;
        $this->deletePermissionUseCase = $deletePermissionUseCase;
        $this->listPermissionsValidator = $listPermissionsValidator;
        $this->permissionCreateRequestValidator = $permissionCreateRequestValidator;
    }

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

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

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
                    new OA\Property(property: "name", description: "Nombre del permiso", type: "string", example: "admin.reports.view"),
                    new OA\Property(property: "active", description: "Estado activo del permiso", type: "boolean", example: true),
                    new OA\Property(property: "web", description: "Acceso web del permiso", type: "boolean", example: true)
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
            $body = $this->getJsonBody($request);
            if (!is_array($body)) {
                return $this->validationError(['body' => 'El cuerpo de la petición debe ser un JSON válido.']);
            }
            // Validación estricta de campos requeridos
            if (!isset($body['name']) || !isset($body['active']) || !isset($body['web'])) {
                $missing = [];
                if (!isset($body['name'])) $missing['name'] = 'El nombre del permiso es requerido.';
                if (!isset($body['active'])) $missing['active'] = 'El campo active es requerido.';
                if (!isset($body['web'])) $missing['web'] = 'El campo web es requerido.';
                return $this->validationError($missing);
            }

            $errors = $this->permissionCreateRequestValidator->validate($body);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $name = $body['name'];
            $active = $this->parseBool($body['active']);
            $web = $this->parseBool($body['web']);

            $requestDTO = new CreatePermissionRequestDTO(
                name: $name,
                active: $active,
                web: $web
            );

            $response = $this->createPermissionUseCase->execute($requestDTO);
            return $this->created($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

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

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

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
        $id = $request->getAttribute('id');
        try {
            $this->deletePermissionUseCase->execute((int) $id);
            return $this->noContent();
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

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
