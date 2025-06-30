<?php

namespace itaxcix\Infrastructure\Web\Controller\api\User;

use itaxcix\Core\Handler\ContactType\ContactTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeListUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeRequestDTO;
use itaxcix\Shared\Validators\useCases\ContactType\ContactTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContactTypeController extends AbstractController
{
    private ContactTypeListUseCaseHandler $listHandler;
    private ContactTypeCreateUseCaseHandler $createHandler;
    private ContactTypeUpdateUseCaseHandler $updateHandler;
    private ContactTypeDeleteUseCaseHandler $deleteHandler;
    private ContactTypeValidator $validator;

    public function __construct(
        ContactTypeListUseCaseHandler $listHandler,
        ContactTypeCreateUseCaseHandler $createHandler,
        ContactTypeUpdateUseCaseHandler $updateHandler,
        ContactTypeDeleteUseCaseHandler $deleteHandler,
        ContactTypeValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/contact-types",
        operationId: "getContactTypes",
        description: "Obtiene tipos de contacto con búsqueda, filtros y paginación avanzada.",
        summary: "Lista tipos de contacto con funcionalidades avanzadas.",
        security: [["bearerAuth" => []]],
        tags: ["Contact Types"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "limit", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre (contiene)", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Parameter(name: "onlyActive", description: "Incluir solo activos", in: "query", required: false, schema: new OA\Schema(type: "boolean", default: false))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de tipos de contacto con metadatos avanzados",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/ContactTypeResponseDTO")),
                    new OA\Property(property: "total", type: "integer", example: 25),
                    new OA\Property(property: "page", type: "integer", example: 1),
                    new OA\Property(property: "limit", type: "integer", example: 15)
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        // Extraer y validar parámetros
        $page = max(1, (int)($queryParams['page'] ?? 1));
        $limit = max(1, min(100, (int)($queryParams['limit'] ?? 15)));
        $name = isset($queryParams['name']) ? trim((string)$queryParams['name']) : null;
        $active = isset($queryParams['active']) ? filter_var($queryParams['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
        $sortBy = (string)($queryParams['sortBy'] ?? 'name');
        $sortDirection = strtoupper((string)($queryParams['sortDirection'] ?? 'ASC'));
        $onlyActive = filter_var($queryParams['onlyActive'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Limpiar valores vacíos
        if ($name === '') $name = null;

        // Si onlyActive es true, forzar active = true
        if ($onlyActive) {
            $active = true;
        }

        // Validar parámetros
        $validationErrors = $this->validator->validatePagination($queryParams);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        // Crear DTO
        $paginationRequest = new ContactTypePaginationRequestDTO(
            page: $page,
            limit: $limit,
            name: $name,
            active: $active,
            sortBy: $sortBy,
            sortDirection: $sortDirection
        );

        $result = $this->listHandler->handle($paginationRequest);

        return $this->ok($result);
    }

    #[OA\Post(
        path: "/admin/contact-types",
        operationId: "createContactType",
        description: "Crea un nuevo tipo de contacto con validaciones avanzadas.",
        summary: "Crear tipo de contacto",
        security: [["bearerAuth" => []]],
        tags: ["Contact Types"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/ContactTypeRequestDTO")
    )]
    #[OA\Response(
        response: 201,
        description: "Tipo de contacto creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Creado"),
                new OA\Property(property: "data", ref: "#/components/schemas/ContactTypeResponseDTO")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Datos de entrada inválidos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El nombre es obligatorio")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonBody($request);

        $validationErrors = $this->validator->validateCreate($data);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        $requestDTO = new ContactTypeRequestDTO(
            name: trim($data['name']),
            active: $data['active'] ?? true
        );

        $result = $this->createHandler->handle($requestDTO);

        return $this->created($result);
    }

    #[OA\Put(
        path: "/admin/contact-types/{id}",
        operationId: "updateContactType",
        description: "Actualiza un tipo de contacto existente con validaciones avanzadas.",
        summary: "Actualizar tipo de contacto",
        security: [["bearerAuth" => []]],
        tags: ["Contact Types"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del tipo de contacto",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", minimum: 1)
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/ContactTypeRequestDTO")
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de contacto actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: "#/components/schemas/ContactTypeResponseDTO")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Tipo de contacto no encontrado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "No encontrado"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Tipo de contacto no encontrado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $this->getJsonBody($request);

        $validationErrors = $this->validator->validateUpdate($data, $id);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        $requestDTO = new ContactTypeRequestDTO(
            name: trim($data['name']),
            active: $data['active'] ?? true
        );

        $result = $this->updateHandler->handle($id, $requestDTO);

        if ($result === null) {
            return $this->error('Tipo de contacto no encontrado', 404);
        }

        return $this->ok($result);
    }

    #[OA\Delete(
        path: "/admin/contact-types/{id}",
        operationId: "deleteContactType",
        description: "Elimina un tipo de contacto existente con validaciones de integridad.",
        summary: "Eliminar tipo de contacto",
        security: [["bearerAuth" => []]],
        tags: ["Contact Types"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del tipo de contacto",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", minimum: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de contacto eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "deleted", type: "boolean", example: true)
                ], type: "object")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Tipo de contacto no encontrado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "No encontrado"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Tipo de contacto no encontrado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        $result = $this->deleteHandler->handle($id);

        if (!$result) {
            return $this->error('Tipo de contacto no encontrado', 404);
        }

        return $this->ok(['deleted' => true]);
    }
}

// Schemas para OpenAPI
#[OA\Schema(
    schema: "ContactTypeRequestDTO",
    required: ["name"],
    properties: [
        new OA\Property(property: "name", type: "string", minLength: 2, maxLength: 100, example: "Teléfono móvil", description: "Nombre del tipo de contacto"),
        new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo (opcional, por defecto true)")
    ],
    type: "object"
)]
class ContactTypeRequestSchema {}

#[OA\Schema(
    schema: "ContactTypeResponseDTO",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1, description: "ID único del tipo de contacto"),
        new OA\Property(property: "name", type: "string", example: "Teléfono móvil", description: "Nombre del tipo de contacto"),
        new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del tipo de contacto")
    ],
    type: "object"
)]
class ContactTypeResponseSchema {}
