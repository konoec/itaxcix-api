<?php

namespace itaxcix\Infrastructure\Web\Controller\api\location;

use InvalidArgumentException;
use itaxcix\Core\Handler\Department\DepartmentCreateUseCaseHandler;
use itaxcix\Core\Handler\Department\DepartmentDeleteUseCaseHandler;
use itaxcix\Core\Handler\Department\DepartmentListUseCaseHandler;
use itaxcix\Core\Handler\Department\DepartmentUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Department\DepartmentPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Department\DepartmentRequestDTO;
use itaxcix\Shared\Validators\useCases\Department\DepartmentValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DepartmentController extends AbstractController
{
    private DepartmentListUseCaseHandler $listHandler;
    private DepartmentCreateUseCaseHandler $createHandler;
    private DepartmentUpdateUseCaseHandler $updateHandler;
    private DepartmentDeleteUseCaseHandler $deleteHandler;
    private DepartmentValidator $validator;

    public function __construct(
        DepartmentListUseCaseHandler $listHandler,
        DepartmentCreateUseCaseHandler $createHandler,
        DepartmentUpdateUseCaseHandler $updateHandler,
        DepartmentDeleteUseCaseHandler $deleteHandler,
        DepartmentValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/departments",
        operationId: "getDepartments",
        description: "Obtiene departamentos con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista departamentos con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Departments"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "limit", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre y ubigeo", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "orderBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "ubigeo"], default: "name"))]
    #[OA\Parameter(name: "orderDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de departamentos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "data", type: "array", items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Lima"),
                            new OA\Property(property: "ubigeo", type: "string", example: "15")
                        ],
                        type: "object"
                    )),
                    new OA\Property(property: "pagination", properties: [
                        new OA\Property(property: "page", type: "integer", example: 1),
                        new OA\Property(property: "limit", type: "integer", example: 15),
                        new OA\Property(property: "total", type: "integer", example: 25),
                        new OA\Property(property: "totalPages", type: "integer", example: 2),
                        new OA\Property(property: "hasNext", type: "boolean", example: true),
                        new OA\Property(property: "hasPrev", type: "boolean", example: false)
                    ], type: "object")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $page = max(1, (int)($queryParams['page'] ?? 1));
        $limit = max(1, min(100, (int)($queryParams['limit'] ?? 15)));
        $search = isset($queryParams['search']) ? trim((string)$queryParams['search']) : null;
        $orderBy = (string)($queryParams['orderBy'] ?? 'name');
        $orderDirection = strtoupper((string)($queryParams['orderDirection'] ?? 'ASC'));

        if ($search === '') $search = null;

        // Validar parámetros de paginación
        $validationErrors = $this->validator->validatePagination($queryParams);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        $dto = new DepartmentPaginationRequestDTO(
            page: $page,
            limit: $limit,
            search: $search,
            orderBy: $orderBy,
            orderDirection: $orderDirection
        );

        try {
            $result = $this->listHandler->handle($dto);
            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error("Error al obtener departamentos: " . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/departments",
        operationId: "createDepartment",
        description: "Crea un nuevo departamento para el panel administrativo.",
        summary: "Crea departamento.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Lima", description: "Nombre del departamento"),
                    new OA\Property(property: "ubigeo", type: "string", example: "15", description: "Código ubigeo del departamento (2 dígitos)")
                ],
                type: "object"
            )
        ),
        tags: ["Admin - Departments"]
    )]
    #[OA\Response(
        response: 200,
        description: "Departamento creado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Departamento creado correctamente."),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "Lima"),
                    new OA\Property(property: "ubigeo", type: "string", example: "15")
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

        try {
            $dto = new DepartmentRequestDTO(
                id: null,
                name: $data['name'],
                ubigeo: $data['ubigeo']
            );

            $result = $this->createHandler->handle($dto);
            return $this->ok($result->toArray(), "Departamento creado correctamente.");
        } catch (\Exception $e) {
            return $this->error("Error al crear departamento: " . $e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/departments/{id}",
        operationId: "updateDepartment",
        description: "Actualiza un departamento existente para el panel administrativo.",
        summary: "Actualiza departamento.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Departments"]
    )]
    #[OA\Parameter(name: "id", description: "ID del departamento", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string", example: "Lima", description: "Nombre del departamento"),
                new OA\Property(property: "ubigeo", type: "string", example: "15", description: "Código ubigeo del departamento (2 dígitos)")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Departamento actualizado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Departamento actualizado correctamente."),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "Lima"),
                    new OA\Property(property: "ubigeo", type: "string", example: "15")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $this->getJsonBody($request);

        if ($id <= 0) {
            return $this->error("ID de departamento inválido", 400);
        }

        $validationErrors = $this->validator->validateUpdate($data, $id);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        try {
            $dto = new DepartmentRequestDTO(
                id: $id,
                name: $data['name'],
                ubigeo: $data['ubigeo']
            );

            $result = $this->updateHandler->handle($dto);
            return $this->ok($result->toArray(), "Departamento actualizado correctamente.");
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error("Error al actualizar departamento: " . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/departments/{id}",
        operationId: "deleteDepartment",
        description: "Elimina un departamento para el panel administrativo.",
        summary: "Elimina departamento.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Departments"]
    )]
    #[OA\Parameter(name: "id", description: "ID del departamento", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Departamento eliminado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Departamento eliminado correctamente.")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        if ($id <= 0) {
            return $this->error("ID de departamento inválido", 400);
        }

        try {
            $result = $this->deleteHandler->handle($id);

            if ($result) {
                return $this->ok("Departamento eliminado correctamente.");
            } else {
                return $this->error("No se pudo eliminar el departamento", 404);
            }
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error("Error al eliminar departamento: " . $e->getMessage(), 500);
        }
    }
}
