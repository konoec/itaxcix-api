<?php

namespace itaxcix\Infrastructure\Web\Controller\web\Admin;

use Exception;
use itaxcix\Core\UseCases\Admin\User\GetUserWithRolesUseCase;
use itaxcix\Core\UseCases\Admin\User\AdminUserListUseCase;
use itaxcix\Core\UseCases\Admin\User\GetUserDetailUseCase;
use itaxcix\Core\UseCases\Admin\User\ChangeUserStatusUseCase;
use itaxcix\Core\UseCases\Admin\User\ForceVerifyContactUseCase;
use itaxcix\Core\UseCases\Admin\User\ResetUserPasswordUseCase;
use itaxcix\Core\UseCases\Admin\User\UpdateUserRolesUseCase;
use itaxcix\Core\UseCases\Admin\User\CreateAdminUserUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\Admin\User\GetUserWithRolesRequestDTO;
use itaxcix\Shared\DTO\Admin\User\AdminUserListRequestDTO;
use itaxcix\Shared\DTO\Admin\User\ChangeUserStatusRequestDTO;
use itaxcix\Shared\DTO\Admin\User\ForceVerifyContactRequestDTO;
use itaxcix\Shared\DTO\Admin\User\ResetUserPasswordRequestDTO;
use itaxcix\Shared\DTO\Admin\User\CreateAdminUserRequestDTO;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AdminUserController - Controlador para administración avanzada de usuarios
 */
class AdminUserController extends AbstractController
{
    private GetUserWithRolesUseCase $getUserWithRolesUseCase;
    private AdminUserListUseCase $adminUserListUseCase;
    private GetUserDetailUseCase $getUserDetailUseCase;
    private ChangeUserStatusUseCase $changeUserStatusUseCase;
    private ForceVerifyContactUseCase $forceVerifyContactUseCase;
    private ResetUserPasswordUseCase $resetUserPasswordUseCase;
    private UpdateUserRolesUseCase $updateUserRolesUseCase;
    private CreateAdminUserUseCase $createAdminUserUseCase;

    public function __construct(
        GetUserWithRolesUseCase $getUserWithRolesUseCase,
        AdminUserListUseCase $adminUserListUseCase,
        GetUserDetailUseCase $getUserDetailUseCase,
        ChangeUserStatusUseCase $changeUserStatusUseCase,
        ForceVerifyContactUseCase $forceVerifyContactUseCase,
        ResetUserPasswordUseCase $resetUserPasswordUseCase,
        UpdateUserRolesUseCase $updateUserRolesUseCase,
        CreateAdminUserUseCase $createAdminUserUseCase
    ) {
        $this->getUserWithRolesUseCase = $getUserWithRolesUseCase;
        $this->adminUserListUseCase = $adminUserListUseCase;
        $this->getUserDetailUseCase = $getUserDetailUseCase;
        $this->changeUserStatusUseCase = $changeUserStatusUseCase;
        $this->forceVerifyContactUseCase = $forceVerifyContactUseCase;
        $this->resetUserPasswordUseCase = $resetUserPasswordUseCase;
        $this->updateUserRolesUseCase = $updateUserRolesUseCase;
        $this->createAdminUserUseCase = $createAdminUserUseCase;
    }

    #[OA\Get(
        path: "/api/v1/users",
        operationId: "listUsers",
        description: "Lista usuarios con filtros administrativos avanzados específicos para el sistema de transporte.",
        summary: "Listar usuarios con filtros avanzados",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
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
        description: "Búsqueda por nombre, apellido o documento",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", example: "juan")
    )]
    #[OA\Parameter(
        name: "userType",
        description: "Tipo de usuario",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", enum: ["citizen", "driver", "admin"], example: "driver")
    )]
    #[OA\Parameter(
        name: "driverStatus",
        description: "Estado del conductor",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", enum: ["PENDIENTE", "APROBADO", "RECHAZADO"], example: "PENDIENTE")
    )]
    #[OA\Parameter(
        name: "hasVehicle",
        description: "Filtrar usuarios con vehículo asociado",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", example: true)
    )]
    #[OA\Parameter(
        name: "contactVerified",
        description: "Filtrar usuarios con contactos verificados",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", example: true)
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de usuarios obtenida exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "users", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(
                                    property: "person",
                                    properties: [
                                        new OA\Property(property: "name", type: "string", example: "Juan"),
                                        new OA\Property(property: "lastName", type: "string", example: "Pérez"),
                                        new OA\Property(property: "document", type: "string", example: "12345678")
                                    ]
                                ),
                                new OA\Property(
                                    property: "roles",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                )
                            ]
                        )),
                        new OA\Property(property: "total", type: "integer", example: 150),
                        new OA\Property(property: "page", type: "integer", example: 1),
                        new OA\Property(property: "limit", type: "integer", example: 20),
                        new OA\Property(property: "totalPages", type: "integer", example: 8)
                    ]
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
    public function adminListUsers(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();

            $requestDTO = new AdminUserListRequestDTO(
                page: (int)($queryParams['page'] ?? 1),
                limit: (int)($queryParams['limit'] ?? 20),
                search: $queryParams['search'] ?? null,
                statusId: isset($queryParams['statusId']) ? (int)$queryParams['statusId'] : null,
                roleId: isset($queryParams['roleId']) ? (int)$queryParams['roleId'] : null,
                userType: $queryParams['userType'] ?? null,
                driverStatus: $queryParams['driverStatus'] ?? null,
                hasVehicle: isset($queryParams['hasVehicle']) ? $this->parseBool($queryParams['hasVehicle']) : null,
                contactVerified: isset($queryParams['contactVerified']) ? $this->parseBool($queryParams['contactVerified']) : null
            );

            $response = $this->adminUserListUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Get(
        path: "/api/v1/users/{userId}",
        operationId: "getUserDetails",
        description: "Obtiene información completa de un usuario.",
        summary: "Obtener detalles completos de usuario",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Detalles completos del usuario",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "userId", type: "integer", example: 1),
                        new OA\Property(property: "person", type: "object"),
                        new OA\Property(property: "userStatus", type: "object"),
                        new OA\Property(
                            property: "contacts",
                            type: "array",
                            items: new OA\Items(type: "object")
                        ),
                        new OA\Property(
                            property: "roles",
                            type: "array",
                            items: new OA\Items(type: "object")
                        )
                    ]
                )
            ]
        )
    )]
    public function getUserDetails(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int)$request->getAttribute('userId');
            $response = $this->getUserDetailUseCase->execute($userId);
            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Get(
        path: "/api/v1/users/{userId}/roles",
        operationId: "getUserWithRoles",
        description: "Obtiene los detalles de un usuario específico junto con todos sus roles asignados.",
        summary: "Obtener usuario con sus roles",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Usuario con roles obtenido exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "firstName", type: "string", example: "Juan"),
                                new OA\Property(property: "lastName", type: "string", example: "Pérez"),
                                new OA\Property(property: "document", type: "string", example: "12345678"),
                                new OA\Property(property: "email", type: "string", example: "juan@example.com")
                            ],
                            type: "object"
                        ),
                        new OA\Property(
                            property: "roles",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 2),
                                    new OA\Property(property: "name", type: "string", example: "Editor"),
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
        description: "Usuario no encontrado"
    )]
    public function getUserWithRoles(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int)$request->getAttribute('userId');

            $requestDTO = new GetUserWithRolesRequestDTO(
                userId: $userId
            );

            $response = $this->getUserWithRolesUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/v1/users/{userId}/roles",
        description: "Permite actualizar los roles asignados a un usuario. No requiere motivo de administrador.",
        summary: "Actualizar roles de usuario",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["roleIds"],
                properties: [
                    new OA\Property(property: "roleIds", description: "IDs de los roles a asignar", type: "array", items: new OA\Items(type: "integer"))
                ]
            )
        ),
        tags: ["Admin - Usuarios"],
        parameters: [
            new OA\Parameter(
                name: "userId",
                description: "ID del usuario",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Roles actualizados exitosamente"
            )
        ]
    )]
    public function updateUserRoles(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int)$request->getAttribute('userId');
            $data = $this->getJsonBody($request);

            $response = $this->updateUserRolesUseCase->execute(
                userId: $userId,
                roleIds: $data['roleIds']
            );

            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/v1/users/{userId}/status",
        operationId: "changeUserStatus",
        description: "Cambia el estado de un usuario con registro de auditoría.",
        summary: "Cambiar estado de usuario",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["statusId"],
            properties: [
                new OA\Property(property: "statusId", type: "integer", example: 2)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de usuario actualizado exitosamente"
    )]
    public function changeUserStatus(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int)$request->getAttribute('userId');
            $data = $this->getJsonBody($request);

            $requestDTO = new ChangeUserStatusRequestDTO(
                userId: $userId,
                statusId: (int)$data['statusId']
            );

            $response = $this->changeUserStatusUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Post(
        path: "/api/v1/users/{userId}/verify-contact",
        operationId: "forceVerifyContact",
        description: "Verifica manualmente un contacto de usuario, útil para resolver problemas de verificación.",
        summary: "Verificar contacto manualmente",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["contactId", "adminReason"],
            properties: [
                new OA\Property(property: "contactId", type: "integer", example: 5)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Contacto verificado exitosamente"
    )]
    public function forceVerifyContact(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int)$request->getAttribute('userId');
            $data = $this->getJsonBody($request);

            $requestDTO = new ForceVerifyContactRequestDTO(
                userId: $userId,
                contactId: (int)$data['contactId']
            );

            $response = $this->forceVerifyContactUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Post(
        path: "/api/v1/users/{userId}/reset-password",
        operationId: "resetUserPassword",
        description: "Resetea la contraseña de un usuario con registro de auditoría.",
        summary: "Resetear contraseña de usuario",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["newPassword"],
            properties: [
                new OA\Property(property: "newPassword", type: "string", example: "nuevaPassword123"),
                new OA\Property(property: "forcePasswordChange", type: "boolean", example: true),
                new OA\Property(property: "adminReason", type: "string", example: "Usuario olvidó su contraseña")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Contraseña reseteada exitosamente"
    )]
    public function resetUserPassword(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int)$request->getAttribute('userId');
            $data = $this->getJsonBody($request);

            $requestDTO = new ResetUserPasswordRequestDTO(
                userId: $userId,
                newPassword: $data['newPassword'],
                forcePasswordChange: $data['forcePasswordChange'] ?? true,
                adminReason: $data['adminReason'] ?? null
            );

            $response = $this->resetUserPasswordUseCase->execute($requestDTO);
            return $this->ok($response);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    #[OA\Post(
        path: "/api/v1/users",
        operationId: "createAdminUser",
        description: "Crea un nuevo usuario administrador ingresando solo el documento DNI. El sistema obtiene automáticamente el nombre y apellido mediante una simulación de API externa (similar al validador de documentos).",
        summary: "Crear usuario administrador con DNI",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Usuarios"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["document", "email", "password", "area", "position"],
            properties: [
                new OA\Property(property: "document", type: "string", example: "12345678", description: "Documento DNI del usuario - El nombre y apellido se obtienen automáticamente"),
                new OA\Property(property: "email", type: "string", example: "admin@itaxcix.com"),
                new OA\Property(property: "password", type: "string", example: "Password@123", description: "Mínimo 8 caracteres, debe contener mayúscula, minúscula y número"),
                new OA\Property(property: "area", type: "string", example: "Sistemas"),
                new OA\Property(property: "position", type: "string", example: "Administrador General")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Usuario administrador creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Usuario administrador creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "firstName", type: "string", example: "JUAN", description: "Nombre obtenido automáticamente de la fake API"),
                                new OA\Property(property: "lastName", type: "string", example: "PEREZ GONZALES", description: "Apellido obtenido automáticamente de la fake API"),
                                new OA\Property(property: "document", type: "string", example: "12345678"),
                                new OA\Property(property: "email", type: "string", example: "admin@itaxcix.com"),
                                new OA\Property(property: "role", type: "string", example: "ADMINISTRADOR"),
                                new OA\Property(property: "area", type: "string", example: "Sistemas"),
                                new OA\Property(property: "position", type: "string", example: "Administrador General")
                            ]
                        )
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error de validación - Datos inválidos o usuario ya existe"
    )]
    #[OA\Response(
        response: 401,
        description: "No autorizado - Token inválido o expirado"
    )]
    #[OA\Response(
        response: 403,
        description: "Acceso denegado - Sin permisos de administración"
    )]
    public function createAdminUser(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $requestDTO = new CreateAdminUserRequestDTO(
                document: $data['document'],
                email: $data['email'],
                password: $data['password'],
                area: $data['area'],
                position: $data['position']
            );

            $response = $this->createAdminUserUseCase->execute($requestDTO);
            return $this->created($response);

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
