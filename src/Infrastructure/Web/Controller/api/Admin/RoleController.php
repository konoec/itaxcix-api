<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admin;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admin\RoleCreateUseCase;
use itaxcix\Core\UseCases\Admin\RoleDeleteUseCase;
use itaxcix\Core\UseCases\Admin\RoleListUseCase;
use itaxcix\Core\UseCases\Admin\RoleUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RoleCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleDeleteRequestDTO;
use itaxcix\Shared\Validators\useCases\Admin\RoleCreateRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\RoleUpdateRequestValidator;
use itaxcix\Shared\Validators\useCases\Admin\RoleDeleteRequestValidator;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Attributes as OA;

class RoleController extends AbstractController
{
    private RoleCreateUseCase $roleCreateUseCase;
    private RoleUpdateUseCase $roleUpdateUseCase;
    private RoleDeleteUseCase $roleDeleteUseCase;
    private RoleListUseCase $roleListUseCase;

    public function __construct(
        RoleCreateUseCase $roleCreateUseCase,
        RoleUpdateUseCase $roleUpdateUseCase,
        RoleDeleteUseCase $roleDeleteUseCase,
        RoleListUseCase $roleListUseCase
    ) {
        $this->roleCreateUseCase = $roleCreateUseCase;
        $this->roleUpdateUseCase = $roleUpdateUseCase;
        $this->roleDeleteUseCase = $roleDeleteUseCase;
        $this->roleListUseCase = $roleListUseCase;
    }

    #[OA\Post(
        path: "/admin/role/create",
        operationId: "createRole",
        description: "Crea un nuevo rol en el sistema.",
        summary: "Crear rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RoleCreateRequestDTO::class)
        ),
        tags: ["Role"]
    )]
    #[OA\Response(
        response: 201,
        description: "Rol creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Creado"),
                new OA\Property(property: "data", ref: RoleResponseDTO::class),
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
            $validator = new RoleCreateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RoleCreateRequestDTO(
                name: $data['name'],
                active: $data['active'] ?? true,
                web: $data['web'] ?? false
            );
            $result = $this->roleCreateUseCase->execute($dto);
            return $this->created($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/admin/role/update",
        operationId: "updateRole",
        description: "Actualiza un rol existente.",
        summary: "Actualizar rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RoleUpdateRequestDTO::class)
        ),
        tags: ["Role"]
    )]
    #[OA\Response(
        response: 200,
        description: "Rol actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: RoleResponseDTO::class),
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new RoleUpdateRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RoleUpdateRequestDTO(
                id: $data['id'],
                name: $data['name'],
                active: $data['active'] ?? true,
                web: $data['web'] ?? false
            );
            $result = $this->roleUpdateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/admin/role/delete",
        operationId: "deleteRole",
        description: "Elimina un rol del sistema.",
        summary: "Eliminar rol",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RoleDeleteRequestDTO::class)
        ),
        tags: ["Role"]
    )]
    #[OA\Response(
        response: 204,
        description: "Rol eliminado exitosamente (sin contenido)"
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new RoleDeleteRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RoleDeleteRequestDTO(
                id: $data['id']
            );
            $this->roleDeleteUseCase->execute($dto);
            return $this->noContent();
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/admin/role/list",
        operationId: "listRoles",
        description: "Lista todos los roles del sistema.",
        summary: "Listar roles",
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
        tags: ["Role"]
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
                            items: new OA\Items(ref: RoleResponseDTO::class)
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

            $result = $this->roleListUseCase->execute($page, $perPage);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
