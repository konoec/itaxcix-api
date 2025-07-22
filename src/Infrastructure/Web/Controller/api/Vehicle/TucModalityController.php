<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\TucModality\TucModalityListUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityCreateUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityUpdateUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityRequestDTO;
use itaxcix\Shared\Validators\useCases\TucModality\TucModalityValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TucModalityController extends AbstractController
{
    private TucModalityListUseCaseHandler $listHandler;
    private TucModalityCreateUseCaseHandler $createHandler;
    private TucModalityUpdateUseCaseHandler $updateHandler;
    private TucModalityDeleteUseCaseHandler $deleteHandler;
    private TucModalityValidator $validator;

    public function __construct(
        TucModalityListUseCaseHandler $listHandler,
        TucModalityCreateUseCaseHandler $createHandler,
        TucModalityUpdateUseCaseHandler $updateHandler,
        TucModalityDeleteUseCaseHandler $deleteHandler,
        TucModalityValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/tuc-modalities",
        operationId: "getTucModalities",
        description: "Obtiene modalidades TUC con búsqueda, filtros y paginación avanzada.",
        summary: "Lista modalidades TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Modality"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de modalidad", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de modalidades TUC con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Modalidades TUC obtenidas exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Taxi"),
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
            $paginationRequest = TucModalityPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener las modalidades TUC: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/tuc-modalities",
        operationId: "createTucModality",
        description: "Crea una nueva modalidad TUC.",
        summary: "Crear nueva modalidad TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Modality"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Taxi", description: "Nombre de la modalidad TUC"),
                new OA\Property(property: "active", description: "Estado activo de la modalidad", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Modalidad TUC creada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Modalidad TUC creada exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Taxi"),
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

            $requestDTO = TucModalityRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear la modalidad TUC: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/tuc-modalities/{id}",
        operationId: "updateTucModality",
        description: "Actualiza una modalidad TUC existente.",
        summary: "Actualizar modalidad TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Modality"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la modalidad TUC", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Taxi modificado", description: "Nombre de la modalidad TUC"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo de la modalidad")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Modalidad TUC actualizada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Modalidad TUC actualizada exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Taxi modificado"),
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

            $requestDTO = TucModalityRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar la modalidad TUC: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/tuc-modalities/{id}",
        operationId: "deleteTucModality",
        description: "Elimina una modalidad TUC.",
        summary: "Eliminar modalidad TUC.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Tuc Modality"]
    )]
    #[OA\Parameter(name: "id", description: "ID de la modalidad TUC", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Modalidad TUC eliminada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Modalidad TUC eliminada exitosamente")
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

            $result = $this->deleteHandler->handle($id);

            if (!$result) {
                return $this->error('No se pudo eliminar la modalidad TUC. Verifique relaciones existentes con TUCs.', 400);
            }

            return $this->ok('Modalidad TUC eliminada exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar la modalidad TUC: ' . $e->getMessage());
        }
    }
}

