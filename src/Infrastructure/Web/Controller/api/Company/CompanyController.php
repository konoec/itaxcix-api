<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Company;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Company\CompanyCreateUseCase;
use itaxcix\Core\UseCases\Company\CompanyDeleteUseCase;
use itaxcix\Core\UseCases\Company\CompanyListUseCase;
use itaxcix\Core\UseCases\Company\CompanyUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;
use itaxcix\Shared\Validators\useCases\Company\CompanyValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CompanyController extends AbstractController
{
    private CompanyListUseCase $listUseCase;
    private CompanyCreateUseCase $createUseCase;
    private CompanyUpdateUseCase $updateUseCase;
    private CompanyDeleteUseCase $deleteUseCase;
    private CompanyValidator $validator;

    public function __construct(
        CompanyListUseCase $listUseCase,
        CompanyCreateUseCase $createUseCase,
        CompanyUpdateUseCase $updateUseCase,
        CompanyDeleteUseCase $deleteUseCase,
        CompanyValidator $validator
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/companies",
        operationId: "getCompanies",
        description: "Obtiene empresas con búsqueda, filtros y paginación.",
        summary: "Lista empresas con funcionalidades de filtrado.",
        security: [["bearerAuth" => []]],
        tags: ["Company"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en RUC y nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "ruc", description: "Filtro por RUC exacto", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre (contiene)", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "ruc", "name", "active"], default: "id"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc"))]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de empresas",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "ruc", type: "string", example: "20123456789"),
                            new OA\Property(property: "name", type: "string", example: "Empresa SAC"),
                            new OA\Property(property: "active", type: "boolean", example: true)
                        ],
                        type: "object"
                    )),
                    new OA\Property(property: "pagination", properties: [
                        new OA\Property(property: "page", type: "integer", example: 1),
                        new OA\Property(property: "perPage", type: "integer", example: 15),
                        new OA\Property(property: "total", type: "integer", example: 50),
                        new OA\Property(property: "totalPages", type: "integer", example: 4)
                    ], type: "object")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        // Validar parámetros de paginación
        $validationErrors = $this->validator->validatePagination($queryParams);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        // Extraer y validar parámetros
        $page = max(1, (int)($queryParams['page'] ?? 1));
        $perPage = max(1, min(100, (int)($queryParams['perPage'] ?? 15)));
        $search = isset($queryParams['search']) && $queryParams['search'] !== '' ? trim((string)$queryParams['search']) : null;
        $ruc = isset($queryParams['ruc']) && $queryParams['ruc'] !== '' ? trim((string)$queryParams['ruc']) : null;
        $name = isset($queryParams['name']) && $queryParams['name'] !== '' ? trim((string)$queryParams['name']) : null;

        // Mejorar el manejo del parámetro active
        $active = null;
        if (isset($queryParams['active']) && $queryParams['active'] !== '') {
            $activeValue = strtolower(trim((string)$queryParams['active']));
            if (in_array($activeValue, ['true', '1', 'yes', 'on'])) {
                $active = true;
            } elseif (in_array($activeValue, ['false', '0', 'no', 'off'])) {
                $active = false;
            }
        }

        $sortBy = isset($queryParams['sortBy']) && $queryParams['sortBy'] !== '' ? (string)$queryParams['sortBy'] : null;
        $sortDirection = strtolower((string)($queryParams['sortDirection'] ?? 'asc'));

        try {
            $paginationRequest = new CompanyPaginationRequestDTO(
                $page,
                $perPage,
                $search,
                $ruc,
                $name,
                $active,
                $sortBy,
                $sortDirection
            );

            $result = $this->listUseCase->execute($paginationRequest);
            return $this->ok($result);

        } catch (\Exception $e) {
            return $this->error('Error al obtener empresas: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/companies",
        operationId: "createCompany",
        description: "Crea una nueva empresa.",
        summary: "Crear empresa",
        security: [["bearerAuth" => []]],
        tags: ["Company"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "ruc", type: "string", example: "20123456789", description: "RUC de 11 dígitos"),
                new OA\Property(property: "name", type: "string", example: "Empresa SAC", description: "Nombre de la empresa (opcional)"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Empresa creada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Empresa creada exitosamente"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "ruc", type: "string", example: "20123456789"),
                    new OA\Property(property: "name", type: "string", example: "Empresa SAC"),
                    new OA\Property(property: "active", type: "boolean", example: true)
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonBody($request);

        // Validar datos de entrada
        $validationErrors = $this->validator->validateCreate($data);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        try {
            $requestDto = new CompanyRequestDTO(
                $data['ruc'],
                $data['name'] ?? null,
                $data['active'] ?? true
            );

            $result = $this->createUseCase->execute($requestDto);
            return $this->ok($result, 'Empresa creada exitosamente', 201);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error interno del servidor', 500);
        }
    }

    #[OA\Put(
        path: "/admin/companies/{id}",
        operationId: "updateCompany",
        description: "Actualiza una empresa existente.",
        summary: "Actualizar empresa",
        security: [["bearerAuth" => []]],
        tags: ["Company"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la empresa", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "ruc", type: "string", example: "20123456789", description: "RUC de 11 dígitos"),
                new OA\Property(property: "name", type: "string", example: "Empresa SAC", description: "Nombre de la empresa"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Empresa actualizada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Empresa actualizada exitosamente"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "ruc", type: "string", example: "20123456789"),
                    new OA\Property(property: "name", type: "string", example: "Empresa SAC"),
                    new OA\Property(property: "active", type: "boolean", example: true)
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $this->getJsonBody($request);

        // Validar datos de entrada
        $validationErrors = $this->validator->validateUpdate($data);
        if (!empty($validationErrors)) {
            return $this->validationError($validationErrors);
        }

        try {
            $requestDto = new CompanyRequestDTO(
                $data['ruc'],
                $data['name'] ?? null,
                $data['active'] ?? true
            );

            $result = $this->updateUseCase->execute($id, $requestDto);
            return $this->ok($result, 'Empresa actualizada exitosamente');

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error interno del servidor', 500);
        }
    }

    #[OA\Delete(
        path: "/admin/companies/{id}",
        operationId: "deleteCompany",
        description: "Elimina una empresa.",
        summary: "Eliminar empresa",
        security: [["bearerAuth" => []]],
        tags: ["Company"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la empresa", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Empresa eliminada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Empresa eliminada exitosamente")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        try {
            $this->deleteUseCase->execute($id);
            return $this->ok('Empresa eliminada exitosamente');

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error interno del servidor', 500);
        }
    }
}
