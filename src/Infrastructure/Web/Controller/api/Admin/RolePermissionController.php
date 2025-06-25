<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admin;

use InvalidArgumentException;

use itaxcix\Core\UseCases\Admin\RolePermissionCreateUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionDeleteUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionListUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionDeleteRequestDTO;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\Validators\useCases\Admin\RolePermissionCreateRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\RolePermissionDeleteRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\RolePermissionUpdateRequestValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Attributes as OA;

class RolePermissionController extends AbstractController
{
    private RolePermissionCreateUseCase $rolePermissionCreateUseCase;
    private RolePermissionUpdateUseCase $rolePermissionUpdateUseCase;
    private RolePermissionDeleteUseCase $rolePermissionDeleteUseCase;
    private RolePermissionListUseCase $rolePermissionListUseCase;
    public function __construct(
        RolePermissionCreateUseCase $rolePermissionCreateUseCase,
        RolePermissionUpdateUseCase $rolePermissionUpdateUseCase,
        RolePermissionDeleteUseCase $rolePermissionDeleteUseCase,
        RolePermissionListUseCase $rolePermissionListUseCase
    ) {
        $this->rolePermissionCreateUseCase = $rolePermissionCreateUseCase;
        $this->rolePermissionUpdateUseCase = $rolePermissionUpdateUseCase;
        $this->rolePermissionDeleteUseCase = $rolePermissionDeleteUseCase;
        $this->rolePermissionListUseCase = $rolePermissionListUseCase;
    }

    #[OA\Post(
        path: "/admin/role-permission/create",
        operationId: "createRolePermission",
        description: "Asigna un permiso a un rol.",
        summary: "Asignar permiso a rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RolePermissionCreateRequestDTO::class)
        ),
        tags: ["RolePermission"]
    )]
    #[OA\Response(
        response: 201,
        description: "Asignación exitosa",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Creado"),
                new OA\Property(property: "data", ref: RolePermissionResponseDTO::class),
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Errores de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Campos requeridos faltantes")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 401,
        description: "No autorizado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "No autorizado"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Token inválido o expirado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new RolePermissionCreateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RolePermissionCreateRequestDTO(
                roleId: $data['roleId'],
                permissionId: $data['permissionId'],
                active: $data['active'] ?? true
            );
            $result = $this->rolePermissionCreateUseCase->execute($dto);
            return $this->created($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/role-permission/update",
        operationId: "updateRolePermission",
        description: "Actualiza la asignación de un permiso a un rol.",
        summary: "Actualizar asignación de permiso a rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RolePermissionUpdateRequestDTO::class)
        ),
        tags: ["RolePermission"]
    )]
    #[OA\Response(
        response: 200,
        description: "Actualización exitosa",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: RolePermissionResponseDTO::class),
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new RolePermissionUpdateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RolePermissionUpdateRequestDTO(
                id: $data['id'],
                roleId: $data['roleId'],
                permissionId: $data['permissionId'],
                active: $data['active'] ?? true
            );
            $result = $this->rolePermissionUpdateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/admin/role-permission/delete",
        operationId: "deleteRolePermission",
        description: "Elimina la asignación de un permiso a un rol.",
        summary: "Eliminar asignación de permiso a rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RolePermissionDeleteRequestDTO::class)
        ),
        tags: ["RolePermission"]
    )]
    #[OA\Response(
        response: 204,
        description: "Eliminación exitosa (sin contenido)"
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new RolePermissionDeleteRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RolePermissionDeleteRequestDTO(
                id: $data['id']
            );
            $this->rolePermissionDeleteUseCase->execute($dto);
            return $this->noContent();
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/admin/role-permission/list",
        operationId: "listRolePermissions",
        description: "Lista todas las asignaciones de permisos a roles.",
        summary: "Listar asignaciones de permisos a roles",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "page",
                description: "Número de página",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "perPage",
                description: "Cantidad de elementos por página",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ],
        tags: ["RolePermission"]
    )]
    #[OA\Response(
        response: 200,
        description: "Listado exitoso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(
                            property: "items",
                            type: "array",
                            items: new OA\Items(ref: RolePermissionResponseDTO::class)
                        ),
                        new OA\Property(
                            property: "meta",
                            properties: [
                                new OA\Property(property: "total", type: "integer", example: 50),
                                new OA\Property(property: "perPage", type: "integer", example: 10),
                                new OA\Property(property: "currentPage", type: "integer", example: 1),
                                new OA\Property(property: "lastPage", type: "integer", example: 5)
                            ],
                            type: "object"
                        )
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $page = (int)($queryParams['page'] ?? 1);
            $perPage = (int)($queryParams['perPage'] ?? 10);

            $result = $this->rolePermissionListUseCase->execute($page, $perPage);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
