<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admin;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admin\UserRoleCreateUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleDeleteUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleListUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleDeleteRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleUpdateRequestDTO;
use itaxcix\Shared\Validators\useCases\Admin\UserRoleCreateRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\UserRoleDeleteRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\UserRoleUpdateRequestValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserRoleController extends AbstractController
{
    private UserRoleCreateUseCase $userRoleCreateUseCase;
    private UserRoleUpdateUseCase $userRoleUpdateUseCase;
    private UserRoleDeleteUseCase $userRoleDeleteUseCase;
    private UserRoleListUseCase $userRoleListUseCase;
    public function __construct(
        UserRoleCreateUseCase $userRoleCreateUseCase,
        UserRoleUpdateUseCase $userRoleUpdateUseCase,
        UserRoleDeleteUseCase $userRoleDeleteUseCase,
        UserRoleListUseCase $userRoleListUseCase
    ) {
        $this->userRoleCreateUseCase = $userRoleCreateUseCase;
        $this->userRoleUpdateUseCase = $userRoleUpdateUseCase;
        $this->userRoleDeleteUseCase = $userRoleDeleteUseCase;
        $this->userRoleListUseCase = $userRoleListUseCase;
    }

    #[OA\Post(
        path: "/admin/user-role/create",
        operationId: "createUserRole",
        description: "Asigna un rol a un usuario.",
        summary: "Asignar rol a usuario",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: UserRoleCreateRequestDTO::class)
        ),
        tags: ["UserRole"]
    )]
    #[OA\Response(
        response: 201,
        description: "Asignación exitosa",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Creado"),
                new OA\Property(property: "data", ref: UserRoleResponseDTO::class),
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
            $validator = new UserRoleCreateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new UserRoleCreateRequestDTO(
                userId: $data['userId'],
                roleId: $data['roleId'],
                active: $data['active'] ?? true
            );
            $result = $this->userRoleCreateUseCase->execute($dto);
            return $this->created($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/user-role/update",
        operationId: "updateUserRole",
        description: "Actualiza la asignación de un rol a un usuario.",
        summary: "Actualizar asignación de rol a usuario",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: UserRoleUpdateRequestDTO::class)
        ),
        tags: ["UserRole"]
    )]
    #[OA\Response(
        response: 200,
        description: "Actualización exitosa",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: UserRoleResponseDTO::class),
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new UserRoleUpdateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new UserRoleUpdateRequestDTO(
                id: $data['id'],
                userId: $data['userId'],
                roleId: $data['roleId'],
                active: $data['active'] ?? true
            );
            $result = $this->userRoleUpdateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/admin/user-role/delete",
        operationId: "deleteUserRole",
        description: "Elimina la asignación de un rol a un usuario.",
        summary: "Eliminar asignación de rol a usuario",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: UserRoleDeleteRequestDTO::class)
        ),
        tags: ["UserRole"]
    )]
    #[OA\Response(
        response: 204,
        description: "Eliminación exitosa (sin contenido)"
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new UserRoleDeleteRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new UserRoleDeleteRequestDTO(
                id: $data['id']
            );
            $this->userRoleDeleteUseCase->execute($dto);
            return $this->noContent();
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/admin/user-role/list",
        operationId: "listUserRoles",
        description: "Lista todas las asignaciones de roles a usuarios.",
        summary: "Listar asignaciones de roles a usuarios",
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
        tags: ["UserRole"]
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
                            items: new OA\Items(ref: UserRoleResponseDTO::class)
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

            $result = $this->userRoleListUseCase->execute($page, $perPage);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
