<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admin;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admin\PermissionCreateUseCase;
use itaxcix\Core\UseCases\Admin\PermissionDeleteUseCase;
use itaxcix\Core\UseCases\Admin\PermissionListUseCase;
use itaxcix\Core\UseCases\Admin\PermissionUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\PermissionCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionDeleteRequestDTO;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\Validators\useCases\Admin\PermissionCreateRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\PermissionDeleteRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\PermissionUpdateRequestValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Attributes as OA;

class PermissionController extends AbstractController
{
    private PermissionCreateUseCase $permissionCreateUseCase;
    private PermissionUpdateUseCase $permissionUpdateUseCase;
    private PermissionDeleteUseCase $permissionDeleteUseCase;
    private PermissionListUseCase $permissionListUseCase;

    public function __construct(
        PermissionCreateUseCase $permissionCreateUseCase,
        PermissionUpdateUseCase $permissionUpdateUseCase,
        PermissionDeleteUseCase $permissionDeleteUseCase,
        PermissionListUseCase $permissionListUseCase
    ) {
        $this->permissionCreateUseCase = $permissionCreateUseCase;
        $this->permissionUpdateUseCase = $permissionUpdateUseCase;
        $this->permissionDeleteUseCase = $permissionDeleteUseCase;
        $this->permissionListUseCase = $permissionListUseCase;
    }

    #[OA\Post(
        path: "/admin/permission/create",
        operationId: "createPermission",
        description: "Crea un nuevo permiso en el sistema.",
        summary: "Crear permiso",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: PermissionCreateRequestDTO::class)
        ),
        tags: ["Permission"]
    )]
    #[OA\Response(
        response: 201,
        description: "Permiso creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Creado"),
                new OA\Property(property: "data", ref: PermissionResponseDTO::class),
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
            $validator = new PermissionCreateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new PermissionCreateRequestDTO(
                name: $data['name'],
                active: $data['active'] ?? true,
                web: $data['web'] ?? false
            );
            $result = $this->permissionCreateUseCase->execute($dto);
            return $this->created($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/permission/update",
        operationId: "updatePermission",
        description: "Actualiza un permiso existente.",
        summary: "Actualizar permiso",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: PermissionUpdateRequestDTO::class)
        ),
        tags: ["Permission"]
    )]
    #[OA\Response(
        response: 200,
        description: "Permiso actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: PermissionResponseDTO::class),
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new PermissionUpdateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new PermissionUpdateRequestDTO(
                id: $data['id'],
                name: $data['name'],
                active: $data['active'] ?? true,
                web: $data['web'] ?? false
            );
            $result = $this->permissionUpdateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/admin/permission/delete",
        operationId: "deletePermission",
        description: "Elimina un permiso del sistema.",
        summary: "Eliminar permiso",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: PermissionDeleteRequestDTO::class)
        ),
        tags: ["Permission"]
    )]
    #[OA\Response(
        response: 204,
        description: "Permiso eliminado exitosamente (sin contenido)"
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new PermissionDeleteRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new PermissionDeleteRequestDTO(
                id: $data['id']
            );
            $this->permissionDeleteUseCase->execute($dto);
            return $this->noContent();
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/admin/permission/list",
        operationId: "listPermissions",
        description: "Lista todos los permisos del sistema.",
        summary: "Listar permisos",
        security: [["bearerAuth" => []]],
        tags: ["Permission"],
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
        ]
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
                            items: new OA\Items(ref: PermissionResponseDTO::class)
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

            $result = $this->permissionListUseCase->execute($page, $perPage);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
