<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Infraction;

use InvalidArgumentException;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusListUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusDeleteUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusRequestDTO;
use itaxcix\Shared\Validators\useCases\InfractionStatus\InfractionStatusValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InfractionStatusController extends AbstractController
{
    private InfractionStatusListUseCaseHandler $listHandler;
    private InfractionStatusCreateUseCaseHandler $createHandler;
    private InfractionStatusUpdateUseCaseHandler $updateHandler;
    private InfractionStatusDeleteUseCaseHandler $deleteHandler;
    private InfractionStatusValidator $validator;

    public function __construct(
        InfractionStatusListUseCaseHandler $listHandler,
        InfractionStatusCreateUseCaseHandler $createHandler,
        InfractionStatusUpdateUseCaseHandler $updateHandler,
        InfractionStatusDeleteUseCaseHandler $deleteHandler,
        InfractionStatusValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/infraction-statuses",
        operationId: "getInfractionStatuses",
        description: "Obtiene estados de infracción con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista estados de infracción con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Status"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de estado", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de estados de infracción con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estados de infracción obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "Pendiente"),
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
            $paginationRequest = InfractionStatusPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los estados de infracción: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/infraction-statuses",
        operationId: "createInfractionStatus",
        description: "Crea un nuevo estado de infracción.",
        summary: "Crear nuevo estado de infracción.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Status"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Pendiente", description: "Nombre del estado"),
                new OA\Property(property: "active", description: "Estado activo del estado", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Estado de infracción creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de infracción creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Pendiente"),
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

            $requestDTO = InfractionStatusRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el estado de infracción: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/infraction-statuses/{id}",
        operationId: "updateInfractionStatus",
        description: "Actualiza un estado de infracción existente.",
        summary: "Actualizar estado de infracción.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado de infracción", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Pendiente Modificado", description: "Nombre del estado"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del estado")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de infracción actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de infracción actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Pendiente Modificado"),
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

            $requestDTO = InfractionStatusRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el estado de infracción: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/infraction-statuses/{id}",
        operationId: "deleteInfractionStatus",
        description: "Elimina un estado de infracción.",
        summary: "Eliminar estado de infracción.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Infraction Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado de infracción", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Estado de infracción eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de infracción eliminado exitosamente")
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
                return $this->error('No se pudo eliminar el estado de infracción', 400);
            }

            return $this->ok('Estado de infracción eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el estado de infracción: ' . $e->getMessage());
        }
    }
}

