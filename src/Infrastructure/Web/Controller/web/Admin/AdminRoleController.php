<?php

namespace itaxcix\Infrastructure\Web\Controller\web\Admin;

use itaxcix\Core\UseCases\Admin\Role\ListRolesUseCase;
use itaxcix\Core\UseCases\Admin\Role\CreateRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\UpdateRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\DeleteRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\AssignPermissionsToRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\GetRoleWithPermissionsUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\Admin\Role\ListRolesRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\CreateRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\UpdateRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\DeleteRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\AssignPermissionsToRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\GetRoleWithPermissionsRequestDTO;
use itaxcix\Shared\Validators\Admin\Role\ListRolesValidator;
use itaxcix\Shared\Validators\Admin\Role\CreateRoleValidator;
use itaxcix\Shared\Validators\Admin\Role\UpdateRoleValidator;
use itaxcix\Shared\Validators\Admin\Role\DeleteRoleValidator;
use itaxcix\Shared\Validators\Admin\Role\AssignPermissionsToRoleValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AdminRoleController - Controlador para administración avanzada de roles
 *
 * Este controlador proporciona un CRUD completo de roles con funcionalidades avanzadas:
 * - Listado paginado con filtros (búsqueda, tipo web, estado)
 * - Creación, actualización y eliminación de roles
 * - Gestión masiva de permisos por rol (asignar múltiples permisos de una vez)
 * - Consulta de roles con sus permisos asignados
 *
 * Reemplaza los controladores básicos anteriores con una solución profesional
 * que permite administrar roles de manera eficiente sin asignar permisos uno por uno.
 *
 * @package itaxcix\Infrastructure\Web\Controller\web\Admin
 * @author Sistema de Administración iTaxCix
 */
class AdminRoleController extends AbstractController
{
    private ListRolesUseCase $listRolesUseCase;
    private CreateRoleUseCase $createRoleUseCase;
    private UpdateRoleUseCase $updateRoleUseCase;
    private DeleteRoleUseCase $deleteRoleUseCase;
    private AssignPermissionsToRoleUseCase $assignPermissionsToRoleUseCase;
    private GetRoleWithPermissionsUseCase $getRoleWithPermissionsUseCase;
    private ListRolesValidator $listRolesValidator;
    private CreateRoleValidator $createRoleValidator;
    private UpdateRoleValidator $updateRoleValidator;
    private DeleteRoleValidator $deleteRoleValidator;
    private AssignPermissionsToRoleValidator $assignPermissionsToRoleValidator;

    public function __construct(
        ListRolesUseCase $listRolesUseCase,
        CreateRoleUseCase $createRoleUseCase,
        UpdateRoleUseCase $updateRoleUseCase,
        DeleteRoleUseCase $deleteRoleUseCase,
        AssignPermissionsToRoleUseCase $assignPermissionsToRoleUseCase,
        GetRoleWithPermissionsUseCase $getRoleWithPermissionsUseCase,
        ListRolesValidator $listRolesValidator,
        CreateRoleValidator $createRoleValidator,
        UpdateRoleValidator $updateRoleValidator,
        DeleteRoleValidator $deleteRoleValidator,
        AssignPermissionsToRoleValidator $assignPermissionsToRoleValidator
    ) {
        $this->listRolesUseCase = $listRolesUseCase;
        $this->createRoleUseCase = $createRoleUseCase;
        $this->updateRoleUseCase = $updateRoleUseCase;
        $this->deleteRoleUseCase = $deleteRoleUseCase;
        $this->assignPermissionsToRoleUseCase = $assignPermissionsToRoleUseCase;
        $this->getRoleWithPermissionsUseCase = $getRoleWithPermissionsUseCase;
        $this->listRolesValidator = $listRolesValidator;
        $this->createRoleValidator = $createRoleValidator;
        $this->updateRoleValidator = $updateRoleValidator;
        $this->deleteRoleValidator = $deleteRoleValidator;
        $this->assignPermissionsToRoleValidator = $assignPermissionsToRoleValidator;
    }

    /**
     * Lista roles con paginación y filtros avanzados
     *
     * Endpoint: GET /api/v1/admin/roles
     * Permisos: admin.roles.list
     *
     * Parámetros de consulta:
     * - page (int): Página actual (default: 1)
     * - limit (int): Elementos por página (default: 20, max: 100)
     * - search (string): Búsqueda por nombre de rol
     * - webOnly (bool): Filtrar solo roles web
     * - activeOnly (bool): Filtrar solo roles activos (default: true)
     *
     * Respuesta exitosa (200):
     * {
     *   "success": true,
     *   "data": {
     *     "roles": [...],
     *     "total": 50,
     *     "page": 1,
     *     "limit": 20,
     *     "totalPages": 3
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP
     * @return ResponseInterface Respuesta con lista paginada de roles
     */
    #[OA\Get(
        path: "/api/v1/roles",
        operationId: "listRoles",
        description: "Lista roles del sistema con paginación y filtros avanzados. Permite búsqueda por nombre, filtrado por tipo web y estado.",
        summary: "Listar roles con paginación",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Roles"]
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
        description: "Búsqueda por nombre de rol",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", example: "admin")
    )]
    #[OA\Parameter(
        name: "webOnly",
        description: "Filtrar solo roles web",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", example: true)
    )]
    #[OA\Parameter(
        name: "activeOnly",
        description: "Filtrar solo roles activos",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", example: true)
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de roles obtenida exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "roles",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Administrador"),
                                    new OA\Property(property: "active", type: "boolean", example: true),
                                    new OA\Property(property: "web", type: "boolean", example: true)
                                ]
                            )
                        ),
                        new OA\Property(property: "total", type: "integer", example: 50),
                        new OA\Property(property: "page", type: "integer", example: 1),
                        new OA\Property(property: "limit", type: "integer", example: 20),
                        new OA\Property(property: "totalPages", type: "integer", example: 3)
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
    public function listRoles(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();

            $errors = $this->listRolesValidator->validate($queryParams);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $requestDTO = new ListRolesRequestDTO(
                page: (int)($queryParams['page'] ?? 1),
                limit: (int)($queryParams['limit'] ?? 20),
                search: $queryParams['search'] ?? null,
                webOnly: isset($queryParams['webOnly']) ? (bool)$queryParams['webOnly'] : null,
                activeOnly: isset($queryParams['activeOnly']) ? (bool)$queryParams['activeOnly'] : true
            );

            $response = $this->listRolesUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Crea un nuevo rol en el sistema
     *
     * Endpoint: POST /api/v1/admin/roles
     * Permisos: admin.roles.create
     *
     * Cuerpo de la petición:
     * {
     *   "name": "Nombre del rol",
     *   "active": true,
     *   "web": false
     * }
     *
     * Validaciones:
     * - name: Requerido, mínimo 2 caracteres, máximo 50, único
     * - active: Opcional, booleano (default: true)
     * - web: Opcional, booleano (default: false)
     *
     * Respuesta exitosa (201):
     * {
     *   "success": true,
     *   "data": {
     *     "id": 5,
     *     "name": "Nombre del rol",
     *     "active": true,
     *     "web": false
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP con datos del rol
     * @return ResponseInterface Respuesta con rol creado o errores de validación
     */
    #[OA\Post(
        path: "/api/v1/roles",
        operationId: "createRole",
        description: "Crea un nuevo rol en el sistema con los permisos especificados.",
        summary: "Crear nuevo rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Editor", description: "Nombre del rol"),
                    new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del rol"),
                    new OA\Property(property: "web", type: "boolean", example: true, description: "Acceso web del rol")
                ]
            )
        ),
        tags: ["Admin - Roles"]
    )]
    #[OA\Response(
        response: 201,
        description: "Rol creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Rol creado correctamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 8),
                        new OA\Property(property: "name", type: "string", example: "Editor"),
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
        description: "Conflicto - Rol ya existe"
    )]
    public function createRole(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $errors = $this->createRoleValidator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $requestDTO = new CreateRoleRequestDTO(
                name: $data['name'],
                active: $data['active'] ?? true,
                web: $data['web'] ?? false
            );

            $response = $this->createRoleUseCase->execute($requestDTO);
            return $this->created($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Asigna múltiples permisos a un rol de forma masiva
     */
    #[OA\Post(
        path: "/api/v1/roles/{roleId}/permissions",
        operationId: "assignPermissionsToRole",
        description: "Funcionalidad principal que permite asignar múltiples permisos a un rol de una sola vez, reemplazando las asignaciones actuales. Soluciona el problema de tener que asignar permisos uno por uno.",
        summary: "Asignar permisos masivamente a un rol",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["permissionIds"],
                properties: [
                    new OA\Property(
                        property: "permissionIds",
                        type: "array",
                        items: new OA\Items(type: "integer"),
                        example: [1, 2, 3, 4, 5, 6, 7, 8],
                        description: "Array de IDs de permisos a asignar al rol"
                    )
                ]
            )
        ),
        tags: ["Admin - Roles"]
    )]
    #[OA\Parameter(
        name: "roleId",
        description: "ID del rol al que se asignarán los permisos",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Permisos asignados exitosamente al rol",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Administrador"),
                        new OA\Property(property: "active", type: "boolean", example: true),
                        new OA\Property(property: "web", type: "boolean", example: true),
                        new OA\Property(
                            property: "permissions",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "admin.roles.list"),
                                    new OA\Property(property: "active", type: "boolean", example: true),
                                    new OA\Property(property: "web", type: "boolean", example: true)
                                ]
                            )
                        )
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error de validación - Rol no encontrado o permisos inválidos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Rol no encontrado")
            ]
        )
    )]
    public function assignPermissionsToRole(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $roleId = (int)$request->getAttribute('roleId');

            $data['roleId'] = $roleId;
            $errors = $this->assignPermissionsToRoleValidator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $requestDTO = new AssignPermissionsToRoleRequestDTO(
                roleId: $roleId,
                permissionIds: $data['permissionIds']
            );

            $response = $this->assignPermissionsToRoleUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Actualiza un rol existente
     *
     * Endpoint: PUT /api/v1/admin/roles/{roleId}
     * Permisos: admin.roles.update
     *
     * Cuerpo de la petición:
     * {
     *   "name": "Nuevo nombre",
     *   "active": false,
     *   "web": true
     * }
     *
     * Validaciones:
     * - name: Requerido, único (excepto el rol actual)
     * - active y web: Opcionales, booleanos
     *
     * @param ServerRequestInterface $request Petición HTTP con datos actualizados
     * @return ResponseInterface Respuesta con rol actualizado
     */
    #[OA\Put(
        path: "/api/v1/roles/{roleId}",
        operationId: "updateRole",
        description: "Actualiza un rol existente en el sistema.",
        summary: "Actualizar rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Editor Avanzado", description: "Nombre del rol"),
                    new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del rol"),
                    new OA\Property(property: "web", type: "boolean", example: true, description: "Acceso web del rol")
                ]
            )
        ),
        tags: ["Admin - Roles"]
    )]
    #[OA\Parameter(
        name: "roleId",
        description: "ID del rol",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Rol actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Rol actualizado correctamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Editor Avanzado"),
                        new OA\Property(property: "active", type: "boolean", example: true),
                        new OA\Property(property: "web", type: "boolean", example: true)
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Rol no encontrado"
    )]
    public function updateRole(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $roleId = (int)$request->getAttribute('roleId');

            $data['id'] = $roleId;
            $errors = $this->updateRoleValidator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $requestDTO = new UpdateRoleRequestDTO(
                id: $roleId,
                name: $data['name'],
                active: $data['active'] ?? true,
                web: $data['web'] ?? false
            );

            $response = $this->updateRoleUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Elimina un rol (soft delete)
     *
     * Endpoint: DELETE /api/v1/admin/roles/{roleId}
     * Permisos: admin.roles.delete
     *
     * Realiza eliminación segura:
     * 1. Verifica que no hay usuarios con este rol asignado
     * 2. Desactiva el rol (soft delete)
     * 3. Desactiva todas las asignaciones de permisos del rol
     *
     * Respuesta exitosa (204): Sin contenido
     *
     * @param ServerRequestInterface $request Petición HTTP con ID del rol
     * @return ResponseInterface Respuesta vacía o error si tiene usuarios asignados
     */
    #[OA\Delete(
        path: "/api/v1/roles/{roleId}",
        operationId: "deleteRole",
        description: "Elimina un rol del sistema (desactivación lógica).",
        summary: "Eliminar rol",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Roles"]
    )]
    #[OA\Parameter(
        name: "roleId",
        description: "ID del rol",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Rol eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Rol eliminado correctamente")
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Rol no encontrado"
    )]
    #[OA\Response(
        response: 409,
        description: "Conflicto - Rol en uso por usuarios"
    )]
    public function deleteRole(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $roleId = (int)$request->getAttribute('roleId');

            $data = ['id' => $roleId];
            $errors = $this->deleteRoleValidator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $requestDTO = new DeleteRoleRequestDTO(id: $roleId);
            $this->deleteRoleUseCase->execute($requestDTO);

            return $this->noContent();

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Obtiene un rol específico con todos sus permisos asignados
     *
     * Endpoint: GET /api/v1/admin/roles/{roleId}/permissions
     * Permisos: admin.roles.list
     *
     * Útil para mostrar la configuración actual de permisos de un rol
     * en interfaces de administración.
     *
     * Respuesta exitosa (200):
     * {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Administrador",
     *     "active": true,
     *     "web": true,
     *     "permissions": [
     *       {"id": 1, "name": "admin.roles.list", ...},
     *       {"id": 2, "name": "admin.roles.create", ...}
     *     ]
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP con ID del rol
     * @return ResponseInterface Respuesta con rol y permisos detallados
     */
    #[OA\Get(
        path: "/api/v1/roles/{roleId}/permissions",
        operationId: "getRoleWithPermissions",
        description: "Obtiene un rol específico con todos sus permisos asignados.",
        summary: "Obtener rol con permisos",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Roles"]
    )]
    #[OA\Parameter(
        name: "roleId",
        description: "ID del rol",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Rol con permisos obtenido exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "roleId", type: "integer", example: 1),
                        new OA\Property(property: "roleName", type: "string", example: "Administrador"),
                        new OA\Property(property: "roleActive", type: "boolean", example: true),
                        new OA\Property(property: "roleWeb", type: "boolean", example: true),
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
                        )
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Rol no encontrado"
    )]
    public function getRoleWithPermissions(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $roleId = (int)$request->getAttribute('roleId');

            $requestDTO = new GetRoleWithPermissionsRequestDTO(roleId: $roleId);
            $response = $this->getRoleWithPermissionsUseCase->execute($requestDTO);

            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
