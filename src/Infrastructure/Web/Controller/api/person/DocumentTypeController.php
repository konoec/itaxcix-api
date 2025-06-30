<?php

namespace itaxcix\Infrastructure\Web\Controller\api\person;

use InvalidArgumentException;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeCreateUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeDeleteUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeListUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypePaginationRequestDTO;
use itaxcix\Shared\Validators\useCases\DocumentType\DocumentTypeValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DocumentTypeController extends AbstractController
{
    private DocumentTypeListUseCase $listUseCase;
    private DocumentTypeCreateUseCase $createUseCase;
    private DocumentTypeUpdateUseCase $updateUseCase;
    private DocumentTypeDeleteUseCase $deleteUseCase;

    public function __construct(
        DocumentTypeListUseCase $listUseCase,
        DocumentTypeCreateUseCase $createUseCase,
        DocumentTypeUpdateUseCase $updateUseCase,
        DocumentTypeDeleteUseCase $deleteUseCase
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
    }

    #[OA\Get(
        path: "/admin/document-types",
        operationId: "getDocumentTypes",
        description: "Obtiene tipos de documento del sistema con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista tipos de documento con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Document Types"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del tipo de documento", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc"))]
    #[OA\Parameter(name: "onlyActive", description: "Incluir solo activos", in: "query", required: false, schema: new OA\Schema(type: "boolean", default: false))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de tipos de documento con metadatos administrativos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/DocumentTypeResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 10),
                        new OA\Property(property: "perPage", type: "integer", example: 15),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 1),
                        new OA\Property(property: "search", type: "string", example: "cédula"),
                        new OA\Property(property: "filters", type: "object"),
                        new OA\Property(property: "sortBy", type: "string", example: "name"),
                        new OA\Property(property: "sortDirection", type: "string", example: "asc")
                    ], type: "object"),
                    new OA\Property(property: "predefinedTypes", type: "object", description: "Tipos de documento predefinidos organizados por categoría")
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
        $perPage = max(1, min(100, (int)($queryParams['perPage'] ?? 15))); // Default 15 para admin
        $search = isset($queryParams['search']) ? trim((string)$queryParams['search']) : null;
        $name = isset($queryParams['name']) ? trim((string)$queryParams['name']) : null;
        $active = isset($queryParams['active']) ? filter_var($queryParams['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
        $sortBy = (string)($queryParams['sortBy'] ?? 'name');
        $sortDirection = strtolower((string)($queryParams['sortDirection'] ?? 'asc'));
        $onlyActive = filter_var($queryParams['onlyActive'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Limpiar valores vacíos
        if ($search === '') $search = null;
        if ($name === '') $name = null;

        // Crear DTO
        $dto = new DocumentTypePaginationRequestDTO(
            page: $page,
            perPage: $perPage,
            search: $search,
            name: $name,
            active: $active,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            onlyActive: $onlyActive
        );

        // Validar parámetros de ordenamiento
        if (!$dto->isValidSortBy()) {
            return $this->error("Campo de ordenamiento inválido. Valores permitidos: id, name, active", 400);
        }

        if (!$dto->isValidSortDirection()) {
            return $this->error("Dirección de ordenamiento inválida. Valores permitidos: asc, desc", 400);
        }

        try {
            $result = $this->listUseCase->execute($dto);

            // Agregar tipos predefinidos para el panel administrativo
            $result['predefinedTypes'] = DocumentTypeValidator::getPredefinedTypes();

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error("Error al obtener tipos de documento: " . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/document-types",
        operationId: "createDocumentType",
        description: "Crea un nuevo tipo de documento para el panel administrativo.",
        summary: "Crea tipo de documento.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", description: "Nombre del tipo de documento", type: "string", example: "Cédula de Ciudadanía"),
                    new OA\Property(property: "active", description: "Estado activo", type: "boolean", example: true)
                ],
                type: "object"
            )
        ),
        tags: ["Admin - Document Types"]
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de documento creado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de documento creado correctamente."),
                new OA\Property(property: "data", ref: "#/components/schemas/DocumentTypeResponseDTO")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonBody($request);

        // Validar datos
        $errors = DocumentTypeValidator::validate($data);
        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        try {
            $dto = DocumentTypeValidator::createDTO($data);
            $result = $this->createUseCase->execute($dto);

            return $this->ok($result->toArray(), "Tipo de documento creado correctamente.");
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error("Error al crear tipo de documento: " . $e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/document-types/{id}",
        operationId: "updateDocumentType",
        description: "Actualiza un tipo de documento existente para el panel administrativo.",
        summary: "Actualiza tipo de documento.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Document Types"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de documento", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", description: "Nombre del tipo de documento", type: "string", example: "Cédula de Ciudadanía"),
                new OA\Property(property: "active", description: "Estado activo", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tipo de documento actualizado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de documento actualizado correctamente."),
                new OA\Property(property: "data", ref: "#/components/schemas/DocumentTypeResponseDTO")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');
        $data = $this->getJsonBody($request);

        if ($id <= 0) {
            return $this->error("ID inválido", 400);
        }

        // Validar datos
        $errors = DocumentTypeValidator::validate($data);
        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        try {
            $dto = DocumentTypeValidator::createDTO($data);
            $result = $this->updateUseCase->execute($id, $dto);

            return $this->ok($result->toArray(), "Tipo de documento actualizado correctamente.");
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error("Error al actualizar tipo de documento: " . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/document-types/{id}",
        operationId: "deleteDocumentType",
        description: "Elimina un tipo de documento del panel administrativo.",
        summary: "Elimina tipo de documento.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Document Types"]
    )]
    #[OA\Parameter(name: "id", description: "ID del tipo de documento", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Tipo de documento eliminado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Tipo de documento eliminado correctamente.")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');

        if ($id <= 0) {
            return $this->error("ID inválido", 400);
        }

        try {
            $result = $this->deleteUseCase->execute($id);

            if (!$result) {
                return $this->error("No se pudo eliminar el tipo de documento", 500);
            }

            return $this->ok(null, "Tipo de documento eliminado correctamente.");
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error("Error al eliminar tipo de documento: " . $e->getMessage(), 500);
        }
    }
}
