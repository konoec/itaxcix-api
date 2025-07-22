<?php

namespace itaxcix\Infrastructure\Web\Controller\api\User;

use InvalidArgumentException;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeListUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeDeleteUseCaseHandler;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeCreateUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeDeleteUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeListUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeRequestDTO;
use itaxcix\Shared\Validators\useCases\UserCodeType\UserCodeTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserCodeTypeController extends AbstractController
{
    private UserCodeTypeListUseCase $listHandler;
    private UserCodeTypeCreateUseCase $createHandler;
    private UserCodeTypeUpdateUseCase $updateHandler;
    private UserCodeTypeDeleteUseCase $deleteHandler;
    private UserCodeTypeValidator $validator;

    public function __construct(
        UserCodeTypeListUseCase $listHandler,
        UserCodeTypeCreateUseCase $createHandler,
        UserCodeTypeUpdateUseCase $updateHandler,
        UserCodeTypeDeleteUseCase $deleteHandler,
        UserCodeTypeValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/user-code-types",
        operationId: "getUserCodeTypes",
        description: "Obtiene tipos de código de usuario con búsqueda, filtros y paginación avanzada.",
        summary: "Lista tipos de código de usuario.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Code Type"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de tipo de código", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de tipos de código de usuario con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipos de código de usuario obtenidos exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Código A"),
                                    new OA\Property(property: "active", type: "boolean", example: true)
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "total_items", type: "integer", example: 25),
                                new OA\Property(property: "total_pages", type: "integer", example: 2),
                                new OA\Property(property: "has_next_page", type: "boolean", example: true),
                                new OA\Property(property: "has_previous_page", type: "boolean", example: false)
                            ]
                        )
                    ]
                )
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $paginationRequest = UserCodeTypePaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->execute($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los tipos de código de usuario: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/user-code-types",
        operationId: "createUserCodeType",
        description: "Crea un nuevo tipo de código de usuario.",
        summary: "Crear nuevo tipo de código de usuario.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Code Type"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Código A", description: "Nombre del tipo de código de usuario"),
                new OA\Property(property: "active", description: "Estado activo del tipo de código de usuario", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Tipo de código de usuario creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de código de usuario creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Código A"),
                        new OA\Property(property: "active", type: "boolean", example: true)
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

            $validationErrors = $this->validator->validateCreate($data);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $requestDTO = UserCodeTypeRequestDTO::fromArray($data);
            $result = $this->createHandler->execute($requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el tipo de código de usuario: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/user-code-types/{id}",
        operationId: "updateUserCodeType",
        description: "Actualiza un tipo de código de usuario existente.",
        summary: "Actualizar tipo de código de usuario.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Code Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de código de usuario", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Código A modificado", description: "Nombre del tipo de código de usuario"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de código de usuario")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de código de usuario actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de código de usuario actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Código A modificado"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');
            $data = $this->getJsonBody($request);

            $idValidationErrors = $this->validator->validateId($id);
            if (!empty($idValidationErrors)) {
                return $this->validationError($idValidationErrors);
            }

            $validationErrors = $this->validator->validateUpdate($data, $id);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $requestDTO = UserCodeTypeRequestDTO::fromArray($data);
            $result = $this->updateHandler->execute($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el tipo de código de usuario: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/user-code-types/{id}",
        operationId: "deleteUserCodeType",
        description: "Elimina un tipo de código de usuario.",
        summary: "Eliminar tipo de código de usuario.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - User Code Type"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de código de usuario", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Tipo de código de usuario eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de código de usuario eliminado exitosamente")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');

            $validationErrors = $this->validator->validateId($id);
            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $result = $this->deleteHandler->execute($id);

            if (!$result) {
                return $this->error('No se pudo eliminar el tipo de código de usuario. Verifique las relaciones existentes.', 400);
            }

            return $this->ok('Tipo de código de usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el tipo de código de usuario: ' . $e->getMessage());
        }
    }
}

