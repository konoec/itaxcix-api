<?php

namespace itaxcix\Infrastructure\Web\Controller\api\District;

use InvalidArgumentException;
use itaxcix\Core\UseCases\District\DistrictCreateUseCase;
use itaxcix\Core\UseCases\District\DistrictDeleteUseCase;
use itaxcix\Core\UseCases\District\DistrictListUseCase;
use itaxcix\Core\UseCases\District\DistrictUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\District\DistrictPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\District\DistrictRequestDTO;
use itaxcix\Shared\Validators\useCases\District\DistrictValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DistrictController extends AbstractController
{
    private DistrictListUseCase $listUseCase;
    private DistrictCreateUseCase $createUseCase;
    private DistrictUpdateUseCase $updateUseCase;
    private DistrictDeleteUseCase $deleteUseCase;
    private DistrictValidator $validator;

    public function __construct(
        DistrictListUseCase $listUseCase,
        DistrictCreateUseCase $createUseCase,
        DistrictUpdateUseCase $updateUseCase,
        DistrictDeleteUseCase $deleteUseCase,
        DistrictValidator $validator
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/districts",
        operationId: "getDistricts",
        description: "Obtiene distritos con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista distritos con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - District"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre, ubigeo y provincia", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de distrito", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "provinceId", description: "Filtro por ID de provincia", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "ubigeo", description: "Filtro por código ubigeo", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "ubigeo"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc"))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de distritos con metadatos administrativos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/DistrictResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 150),
                        new OA\Property(property: "perPage", type: "integer", example: 15),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 10),
                        new OA\Property(property: "search", type: "string", example: "Lima"),
                        new OA\Property(property: "filters", type: "object"),
                        new OA\Property(property: "sortBy", type: "string", example: "name"),
                        new OA\Property(property: "sortDirection", type: "string", example: "asc")
                    ], type: "object")
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
        $provinceId = isset($queryParams['provinceId']) ? (int)$queryParams['provinceId'] : null;
        $ubigeo = isset($queryParams['ubigeo']) ? trim((string)$queryParams['ubigeo']) : null;
        $sortBy = (string)($queryParams['sortBy'] ?? 'name');
        $sortDirection = strtolower((string)($queryParams['sortDirection'] ?? 'asc'));

        // Limpiar valores vacíos
        if ($search === '') $search = null;
        if ($name === '') $name = null;
        if ($ubigeo === '') $ubigeo = null;
        if ($provinceId === 0) $provinceId = null;

        // Crear DTO
        $dto = new DistrictPaginationRequestDTO(
            page: $page,
            perPage: $perPage,
            search: $search,
            name: $name,
            provinceId: $provinceId,
            ubigeo: $ubigeo,
            sortBy: $sortBy,
            sortDirection: $sortDirection
        );

        try {
            $result = $this->listUseCase->execute($dto);
            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/districts",
        operationId: "createDistrict",
        description: "Crea un nuevo distrito en el sistema.",
        summary: "Crear nuevo distrito.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - District"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "provinceId"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Miraflores", description: "Nombre del distrito"),
                new OA\Property(property: "provinceId", type: "integer", example: 1, description: "ID de la provincia"),
                new OA\Property(property: "ubigeo", type: "string", example: "150122", description: "Código ubigeo del distrito")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Distrito creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Distrito creado exitosamente"),
                new OA\Property(property: "data", ref: "#/components/schemas/DistrictResponseDTO")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $dto = DistrictRequestDTO::fromArray($data);

            // Validar datos
            $errors = $this->validator->validateCreate($dto);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->createUseCase->execute($dto);
            return $this->ok($result->toArray(), "Distrito creado exitosamente", 201);

        } catch (InvalidArgumentException $e) {
            return $this->error("Datos inválidos: " . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/districts/{id}",
        operationId: "updateDistrict",
        description: "Actualiza un distrito existente.",
        summary: "Actualizar distrito.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - District"]
    )]
    #[OA\Parameter(name: "id", description: "ID del distrito", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "provinceId"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Miraflores", description: "Nombre del distrito"),
                new OA\Property(property: "provinceId", type: "integer", example: 1, description: "ID de la provincia"),
                new OA\Property(property: "ubigeo", type: "string", example: "150122", description: "Código ubigeo del distrito")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Distrito actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Distrito actualizado exitosamente"),
                new OA\Property(property: "data", ref: "#/components/schemas/DistrictResponseDTO")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int)$request->getAttribute('id');
            $data = $this->getJsonBody($request);
            $data['id'] = $id;

            $dto = DistrictRequestDTO::fromArray($data);

            // Validar datos
            $errors = $this->validator->validateUpdate($dto);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->updateUseCase->execute($dto);
            return $this->ok($result->toArray(), "Distrito actualizado exitosamente");

        } catch (InvalidArgumentException $e) {
            return $this->error("Datos inválidos: " . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/districts/{id}",
        operationId: "deleteDistrict",
        description: "Elimina un distrito del sistema.",
        summary: "Eliminar distrito.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - District"]
    )]
    #[OA\Parameter(name: "id", description: "ID del distrito", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Distrito eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Distrito eliminado exitosamente")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int)$request->getAttribute('id');

            // Validar ID
            $errors = $this->validator->validateDelete($id);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $success = $this->deleteUseCase->execute($id);

            if ($success) {
                return $this->ok(
                    ["message" => "Distrito eliminado exitosamente"]
                );
            } else {
                return $this->error("No se pudo eliminar el distrito", 500);
            }

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
