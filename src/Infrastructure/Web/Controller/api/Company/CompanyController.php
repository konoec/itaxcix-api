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

    public function __construct(
        CompanyListUseCase $listUseCase,
        CompanyCreateUseCase $createUseCase,
        CompanyUpdateUseCase $updateUseCase,
        CompanyDeleteUseCase $deleteUseCase
    ) {
        $this->listUseCase = $listUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
    }

    #[OA\Get(
        path: "/companies",
        operationId: "getCompanies",
        description: "Obtiene empresas paginadas para administración.",
        summary: "Lista empresas con paginación.",
        security: [["bearerAuth" => []]],
        tags: ["Company"]
    )]
    #[OA\Parameter(
        name: "page",
        description: "Número de página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", default: 1, minimum: 1)
    )]
    #[OA\Parameter(
        name: "perPage",
        description: "Elementos por página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", default: 10, minimum: 1, maximum: 100)
    )]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de empresas",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/CompanyResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 50),
                        new OA\Property(property: "perPage", type: "integer", example: 10),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 5)
                    ], type: "object")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = (int)($queryParams['page'] ?? 1);
        $perPage = (int)($queryParams['perPage'] ?? 10);

        // Validar límites
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $dto = new CompanyPaginationRequestDTO($page, $perPage);
        $result = $this->listUseCase->execute($dto);

        return $this->ok($result);
    }

    #[OA\Post(
        path: "/companies",
        operationId: "createCompany",
        description: "Crea una nueva empresa.",
        summary: "Crea empresa.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CompanyRequestDTO")
        ),
        tags: ["Company"]
    )]
    #[OA\Response(
        response: 200,
        description: "Empresa creada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Empresa creada correctamente.")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new CompanyValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new CompanyRequestDTO(
                id: null,
                ruc: (string)$data['ruc'],
                name: isset($data['name']) ? (string)$data['name'] : null,
                active: $data['active'] ?? true
            );

            $result = $this->createUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/companies/{id}",
        operationId: "updateCompany",
        description: "Actualiza una empresa.",
        summary: "Actualiza empresa.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CompanyRequestDTO")
        ),
        tags: ["Company"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID de la empresa a actualizar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Empresa actualizada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Empresa actualizada correctamente.")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $id = (int)$request->getAttribute('id');

            $validator = new CompanyValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new CompanyRequestDTO(
                id: $id,
                ruc: (string)$data['ruc'],
                name: isset($data['name']) ? (string)$data['name'] : null,
                active: $data['active'] ?? true
            );

            $result = $this->updateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/companies/{id}",
        operationId: "deleteCompany",
        description: "Elimina (desactiva) una empresa.",
        summary: "Elimina empresa.",
        security: [["bearerAuth" => []]],
        tags: ["Company"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID de la empresa a eliminar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Empresa eliminada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Empresa eliminada correctamente.")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int)$request->getAttribute('id');
            $result = $this->deleteUseCase->execute($id);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
