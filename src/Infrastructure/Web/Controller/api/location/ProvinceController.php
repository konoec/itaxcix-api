<?php

namespace itaxcix\Infrastructure\Web\Controller\api\location;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Province\ProvinceCreateUseCase;
use itaxcix\Core\UseCases\Province\ProvinceDeleteUseCase;
use itaxcix\Core\UseCases\Province\ProvinceListUseCase;
use itaxcix\Core\UseCases\Province\ProvinceUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Province\ProvincePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceResponseDTO;
use itaxcix\Shared\Validators\useCases\Province\ProvinceValidator;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProvinceController extends AbstractController
{
    private ProvinceListUseCase $listUseCase;
    private ProvinceCreateUseCase $createUseCase;
    private ProvinceUpdateUseCase $updateUseCase;
    private ProvinceDeleteUseCase $deleteUseCase;
    private ProvinceRepositoryInterface $provinceRepository;
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(
        ProvinceListUseCase $listUseCase,
        ProvinceCreateUseCase $createUseCase,
        ProvinceUpdateUseCase $updateUseCase,
        ProvinceDeleteUseCase $deleteUseCase,
        ProvinceRepositoryInterface $provinceRepository,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->provinceRepository = $provinceRepository;
        $this->departmentRepository = $departmentRepository;
    }

    #[OA\Get(
        path: "/admin/provinces",
        operationId: "getProvinces",
        description: "Obtiene provincias con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista provincias con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Province"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre, ubigeo y departamento", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de provincia", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "departmentId", description: "Filtro por ID de departamento", in: "query", required: false, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "ubigeo", description: "Filtro por código UBIGEO", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "ubigeo"], default: "name"))]
    #[OA\Parameter(name: "sortOrder", description: "Orden de clasificación", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de provincias obtenida exitosamente",
        content: new OA\JsonContent(
            properties: [
                "status" => new OA\Property(property: "status", type: "string", example: "success"),
                "message" => new OA\Property(property: "message", type: "string", example: "Provincias obtenidas exitosamente"),
                "data" => new OA\Property(
                    property: "data",
                    properties: [
                        "data" => new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    "id" => new OA\Property(property: "id", type: "integer", example: 1),
                                    "name" => new OA\Property(property: "name", type: "string", example: "Lima"),
                                    "departmentId" => new OA\Property(property: "departmentId", type: "integer", example: 15),
                                    "departmentName" => new OA\Property(property: "departmentName", type: "string", example: "Lima"),
                                    "ubigeo" => new OA\Property(property: "ubigeo", type: "string", example: "1501")
                                ]
                            )
                        ),
                        "pagination" => new OA\Property(
                            property: "pagination",
                            properties: [
                                "page" => new OA\Property(property: "page", type: "integer", example: 1),
                                "perPage" => new OA\Property(property: "perPage", type: "integer", example: 15),
                                "total" => new OA\Property(property: "total", type: "integer", example: 50),
                                "totalPages" => new OA\Property(property: "totalPages", type: "integer", example: 4)
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
            $validator = new ProvinceValidator($this->provinceRepository, $this->departmentRepository);
            $validationErrors = $validator->validatePagination($params);

            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $paginationRequest = new ProvincePaginationRequestDTO(
                page: (int)($params['page'] ?? 1),
                perPage: (int)($params['perPage'] ?? 15),
                search: $params['search'] ?? null,
                name: $params['name'] ?? null,
                departmentId: isset($params['departmentId']) ? (int)$params['departmentId'] : null,
                ubigeo: $params['ubigeo'] ?? null,
                sortBy: $params['sortBy'] ?? 'name',
                sortOrder: strtoupper($params['sortOrder'] ?? 'ASC')
            );

            $result = $this->listUseCase->execute($paginationRequest);

            return $this->ok($result, 'Provincias obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->error('Error al obtener las provincias: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/provinces",
        operationId: "createProvince",
        description: "Crea una nueva provincia en el sistema.",
        summary: "Crear nueva provincia.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Province"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name", "departmentId", "ubigeo"],
            properties: [
                "name" => new OA\Property(property: "name", type: "string", example: "Huaura", description: "Nombre de la provincia"),
                "departmentId" => new OA\Property(property: "departmentId", type: "integer", example: 15, description: "ID del departamento"),
                "ubigeo" => new OA\Property(property: "ubigeo", type: "string", example: "1509", description: "Código UBIGEO de 4 dígitos")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Provincia creada exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                "status" => new OA\Property(property: "status", type: "string", example: "success"),
                "message" => new OA\Property(property: "message", type: "string", example: "Provincia creada exitosamente"),
                "data" => new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        "id" => new OA\Property(property: "id", type: "integer", example: 1),
                        "name" => new OA\Property(property: "name", type: "string", example: "Huaura"),
                        "departmentId" => new OA\Property(property: "departmentId", type: "integer", example: 15),
                        "departmentName" => new OA\Property(property: "departmentName", type: "string", example: "Lima"),
                        "ubigeo" => new OA\Property(property: "ubigeo", type: "string", example: "1509")
                    ]
                )
            ]
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $requestDTO = new ProvinceRequestDTO(
                name: $data['name'] ?? null,
                departmentId: isset($data['departmentId']) ? (int)$data['departmentId'] : null,
                ubigeo: $data['ubigeo'] ?? null
            );

            $validator = new ProvinceValidator($this->provinceRepository, $this->departmentRepository);
            $validationErrors = $validator->validate($requestDTO);

            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $result = $this->createUseCase->execute($requestDTO);

            return $this->ok($result->toArray(), 'Provincia creada exitosamente', 201);

        } catch (\Exception $e) {
            return $this->error('Error al crear la provincia: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/provinces/{id}",
        operationId: "updateProvince",
        description: "Actualiza una provincia existente.",
        summary: "Actualizar provincia.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Province"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la provincia", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name", "departmentId", "ubigeo"],
            properties: [
                "name" => new OA\Property(property: "name", type: "string", example: "Huaura", description: "Nombre de la provincia"),
                "departmentId" => new OA\Property(property: "departmentId", type: "integer", example: 15, description: "ID del departamento"),
                "ubigeo" => new OA\Property(property: "ubigeo", type: "string", example: "1509", description: "Código UBIGEO de 4 dígitos")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Provincia actualizada exitosamente",
        content: new OA\JsonContent(
            properties: [
                "status" => new OA\Property(property: "status", type: "string", example: "success"),
                "message" => new OA\Property(property: "message", type: "string", example: "Provincia actualizada exitosamente"),
                "data" => new OA\Property(
                    property: "data",
                    properties: [
                        "id" => new OA\Property(property: "id", type: "integer", example: 1),
                        "name" => new OA\Property(property: "name", type: "string", example: "Huaura"),
                        "departmentId" => new OA\Property(property: "departmentId", type: "integer", example: 15),
                        "departmentName" => new OA\Property(property: "departmentName", type: "string", example: "Lima"),
                        "ubigeo" => new OA\Property(property: "ubigeo", type: "string", example: "1509")
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
            $id = (int)$request->getAttribute('id');
            $data = $this->getJsonBody($request);

            if (!$id) {
                return $this->error('ID de provincia requerido', 400);
            }

            $requestDTO = new ProvinceRequestDTO(
                name: $data['name'] ?? null,
                departmentId: isset($data['departmentId']) ? (int)$data['departmentId'] : null,
                ubigeo: $data['ubigeo'] ?? null
            );

            $validator = new ProvinceValidator($this->provinceRepository, $this->departmentRepository);
            $validationErrors = $validator->validate($requestDTO, $id);

            if (!empty($validationErrors)) {
                return $this->validationError($validationErrors);
            }

            $result = $this->updateUseCase->execute($id, $requestDTO);

            return $this->ok($result->toArray(), 'Provincia actualizada exitosamente');

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar la provincia: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/provinces/{id}",
        operationId: "deleteProvince",
        description: "Elimina una provincia del sistema.",
        summary: "Eliminar provincia.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Province"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la provincia", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Provincia eliminada exitosamente",
        content: new OA\JsonContent(
            properties: [
                "status" => new OA\Property(property: "status", type: "string", example: "success"),
                "message" => new OA\Property(property: "message", type: "string", example: "Provincia eliminada exitosamente")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int)$request->getAttribute('id');

            if (!$id) {
                return $this->error('ID de provincia requerido', 400);
            }

            $result = $this->deleteUseCase->execute($id);

            if (!$result) {
                return $this->error('No se pudo eliminar la provincia', 500);
            }

            return $this->ok(null, 'Provincia eliminada exitosamente');

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al eliminar la provincia: ' . $e->getMessage(), 500);
        }
    }
}
