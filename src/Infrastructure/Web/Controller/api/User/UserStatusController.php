<?php

namespace itaxcix\Infrastructure\Web\Controller\api\User;

use InvalidArgumentException;
use Exception;
use itaxcix\Core\UseCases\UserStatus\UserStatusCreateUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusDeleteUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusListUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusRequestDTO;
use itaxcix\Shared\Validators\useCases\UserStatus\UserStatusValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserStatusController extends AbstractController
{
    private UserStatusListUseCase $listUseCase;
    private UserStatusCreateUseCase $createUseCase;
    private UserStatusUpdateUseCase $updateUseCase;
    private UserStatusDeleteUseCase $deleteUseCase;
    private UserStatusValidator $validator;

    public function __construct(
        UserStatusListUseCase $listUseCase,
        UserStatusCreateUseCase $createUseCase,
        UserStatusUpdateUseCase $updateUseCase,
        UserStatusDeleteUseCase $deleteUseCase,
        UserStatusValidator $validator
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/user-statuses",
        operationId: "getUserStatuses",
        description: "Obtiene estados de usuario con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista estados de usuario con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Status"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del estado", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de estados de usuario obtenida exitosamente",
        content: new OA\JsonContent(
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "data" => new OA\Property(
                    property: "data",
                    properties: [
                        "data" => new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    "id" => new OA\Property(property: "id", type: "integer", example: 1),
                                    "name" => new OA\Property(property: "name", type: "string", example: "Activo"),
                                    "active" => new OA\Property(property: "active", type: "boolean", example: true)
                                ],
                                type: "object"
                            )
                        ),
                        "pagination" => new OA\Property(
                            property: "pagination",
                            properties: [
                                "page" => new OA\Property(property: "page", type: "integer", example: 1),
                                "perPage" => new OA\Property(property: "perPage", type: "integer", example: 15),
                                "total" => new OA\Property(property: "total", type: "integer", example: 50),
                                "totalPages" => new OA\Property(property: "totalPages", type: "integer", example: 4),
                                "hasNextPage" => new OA\Property(property: "hasNextPage", type: "boolean", example: true),
                                "hasPreviousPage" => new OA\Property(property: "hasPreviousPage", type: "boolean", example: false)
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
            $params = $request->getQueryParams();

            // Validar parámetros de paginación
            $validationErrors = $this->validator->validatePagination($params);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            // Convertir parámetros
            $page = (int)($params['page'] ?? 1);
            $perPage = (int)($params['perPage'] ?? 15);
            $search = $params['search'] ?? null;
            $name = $params['name'] ?? null;
            $active = isset($params['active']) ? filter_var($params['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
            $sortBy = $params['sortBy'] ?? 'name';
            $sortDirection = strtoupper($params['sortDirection'] ?? 'ASC');

            $paginationRequest = new UserStatusPaginationRequestDTO(
                $page,
                $perPage,
                $search,
                $name,
                $active,
                $sortBy,
                $sortDirection
            );

            $result = $this->listUseCase->execute($paginationRequest);
            return $this->ok($result);

        } catch (Exception $e) {
            return $this->error('Error al obtener estados de usuario: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/user-statuses",
        operationId: "createUserStatus",
        description: "Crear un nuevo estado de usuario en el sistema administrativo.",
        summary: "Crear estado de usuario",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Status"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                "name" => new OA\Property(property: "name", type: "string", example: "Pendiente", description: "Nombre del estado de usuario"),
                "active" => new OA\Property(property: "active", type: "boolean", example: true, description: "Si el estado está activo")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Estado de usuario creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "data" => new OA\Property(
                    property: "data",
                    properties: [
                        "id" => new OA\Property(property: "id", type: "integer", example: 1),
                        "name" => new OA\Property(property: "name", type: "string", example: "Pendiente"),
                        "active" => new OA\Property(property: "active", type: "boolean", example: true)
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar datos
            $validationErrors = $this->validator->validateCreate($data);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $requestDTO = new UserStatusRequestDTO(
                trim($data['name']),
                $data['active'] ?? true
            );

            $result = $this->createUseCase->execute($requestDTO);
            return $this->ok($result, 201);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (Exception $e) {
            return $this->error('Error al crear estado de usuario: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/user-statuses/{id}",
        operationId: "updateUserStatus",
        description: "Actualizar un estado de usuario existente en el sistema administrativo.",
        summary: "Actualizar estado de usuario",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado de usuario", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                "name" => new OA\Property(property: "name", type: "string", example: "Suspendido", description: "Nombre del estado de usuario"),
                "active" => new OA\Property(property: "active", type: "boolean", example: false, description: "Si el estado está activo")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de usuario actualizado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "data" => new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        "id" => new OA\Property(property: "id", type: "integer", example: 1),
                        "name" => new OA\Property(property: "name", type: "string", example: "Suspendido"),
                        "active" => new OA\Property(property: "active", type: "boolean", example: false)
                    ]
                )
            ]
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');
            $data = $this->getJsonBody($request);

            // Validar datos
            $validationErrors = $this->validator->validateUpdate($data, $id);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $requestDTO = new UserStatusRequestDTO(
                trim($data['name']),
                $data['active'] ?? true
            );

            $result = $this->updateUseCase->execute($id, $requestDTO);
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (Exception $e) {
            return $this->error('Error al actualizar estado de usuario: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/user-statuses/{id}",
        operationId: "deleteUserStatus",
        description: "Eliminar un estado de usuario del sistema administrativo.",
        summary: "Eliminar estado de usuario",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado de usuario", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Estado de usuario eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                "success" => new OA\Property(property: "success", type: "boolean", example: true),
                "message" => new OA\Property(property: "message", type: "string", example: "Estado de usuario eliminado exitosamente")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');

            // Validar que existe
            $validationErrors = $this->validator->validateDelete($id);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $this->deleteUseCase->execute($id);
            return $this->ok(['message' => 'Estado de usuario eliminado exitosamente']);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (Exception $e) {
            return $this->error('Error al eliminar estado de usuario: ' . $e->getMessage());
        }
    }
}
