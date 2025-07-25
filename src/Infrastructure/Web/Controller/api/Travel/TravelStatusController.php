<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Travel;

use Exception;
use InvalidArgumentException;
use itaxcix\Core\Handler\TravelStatus\TravelStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusDeleteUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusListUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Travel\CancelTravelUseCase;
use itaxcix\Core\UseCases\Travel\CompleteTravelUseCase;
use itaxcix\Core\UseCases\Travel\RequestNewTravelUseCase;
use itaxcix\Core\UseCases\Travel\RespondToTravelRequestUseCase;
use itaxcix\Core\UseCases\Travel\StartAcceptedTravelUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Travel\RequestTravelDTO;
use itaxcix\Shared\DTO\useCases\Travel\RespondToRequestDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusRequestDTO;
use itaxcix\Shared\Validators\useCases\Travel\RequestTravelValidator;
use itaxcix\Shared\Validators\useCases\Travel\RespondToRequestValidator;
use itaxcix\Shared\Validators\useCases\Travel\TravelIdValidator;
use itaxcix\Shared\Validators\useCases\TravelStatus\TravelStatusValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TravelStatusController extends AbstractController
{
    private CancelTravelUseCase $cancelTravelUseCase;
    private CompleteTravelUseCase $completeTravelUseCase;
    private RespondToTravelRequestUseCase $respondToTravelRequestUseCase;
    private StartAcceptedTravelUseCase $startAcceptedTravelUseCase;
    private RequestNewTravelUseCase $requestNewTravelUseCase;
    private TravelStatusListUseCaseHandler $listHandler;
    private TravelStatusCreateUseCaseHandler $createHandler;
    private TravelStatusUpdateUseCaseHandler $updateHandler;
    private TravelStatusDeleteUseCaseHandler $deleteHandler;
    private TravelStatusValidator $validator;

    public function __construct(
        CancelTravelUseCase $cancelTravelUseCase,
        CompleteTravelUseCase $completeTravelUseCase,
        RespondToTravelRequestUseCase $respondToTravelRequestUseCase,
        StartAcceptedTravelUseCase $startAcceptedTravelUseCase,
        RequestNewTravelUseCase $requestNewTravelUseCase,
        TravelStatusListUseCaseHandler $listHandler,
        TravelStatusCreateUseCaseHandler $createHandler,
        TravelStatusUpdateUseCaseHandler $updateHandler,
        TravelStatusDeleteUseCaseHandler $deleteHandler,
        TravelStatusValidator $validator
    ) {
        $this->cancelTravelUseCase = $cancelTravelUseCase;
        $this->completeTravelUseCase = $completeTravelUseCase;
        $this->respondToTravelRequestUseCase = $respondToTravelRequestUseCase;
        $this->startAcceptedTravelUseCase = $startAcceptedTravelUseCase;
        $this->requestNewTravelUseCase = $requestNewTravelUseCase;
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    // POST /travels - Solicitar un nuevo viaje
    #[OA\Post(
        path: "/travels",
        operationId: "requestTravel",
        description: "Solicita un nuevo viaje. El usuario debe ser un ciudadano registrado. Elige un conductor y define origen y destino.",
        summary: "Solicitar un nuevo viaje",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RequestTravelDTO::class)
        ),
        tags: ["Travels"]
    )]
    #[OA\Response(
        response: 200,
        description: "Solicitud de viaje creada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: TravelResponseDTO::class)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida o error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Conductor o ciudadano no encontrado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Error interno del servidor",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Error interno del servidor"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function requestTravel(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos
            $validator = new RequestTravelValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear al DTO
            $dto = new RequestTravelDTO(
                citizenId: (int)$data['citizenId'],
                driverId: (int)$data['driverId'],
                originLatitude: (float)$data['originLatitude'],
                originLongitude: (float)$data['originLongitude'],
                originDistrict: $data['originDistrict'],
                originAddress: $data['originAddress'],
                destinationLatitude: (float)$data['destinationLatitude'],
                destinationLongitude: (float)$data['destinationLongitude'],
                destinationDistrict: $data['destinationDistrict'],
                destinationAddress: $data['destinationAddress']
            );

            // 4. Ejecutar caso de uso
            $result = $this->requestNewTravelUseCase->execute($dto);

            // 5. Devolver respuesta
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // PATCH /travels/{travelId}/respond - Conductor responde a solicitud
    #[OA\Patch(
        path: "/travels/{travelId}/respond",
        operationId: "respondToRequest",
        description: "El conductor acepta o rechaza una solicitud de viaje. El parámetro 'accepted' debe ser booleano.",
        summary: "Responder a una solicitud de viaje",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RespondToRequestDTO::class)
        ),
        tags: ["Travels"]
    )]
    #[OA\Parameter(
        name: "travelId",
        description: "ID del viaje",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Respuesta registrada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: TravelResponseDTO::class)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida o error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El viaje no está en estado SOLICITADO")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function respondToRequest(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener datos
            $data = $this->getJsonBody($request);
            $data['travelId'] = $request->getAttribute('travelId');

            // 2. Validar
            $validator = new RespondToRequestValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear DTO
            $dto = new RespondToRequestDTO(
                travelId: (int)$data['travelId'],
                accepted: (bool)$data['accepted']
            );

            // 4. Ejecutar caso de uso
            $result = $this->respondToTravelRequestUseCase->execute($dto);

            // 5. Respuesta
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // PATCH /travels/{travelId}/start - Iniciar un viaje aceptado
    #[OA\Patch(
        path: "/travels/{travelId}/start",
        operationId: "startTravel",
        description: "Inicia un viaje previamente aceptado. Solo el conductor asignado puede iniciar el viaje.",
        summary: "Iniciar viaje",
        security: [["bearerAuth" => []]],
        tags: ["Travels"]
    )]
    #[OA\Parameter(
        name: "travelId",
        description: "ID del viaje",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Viaje iniciado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: TravelResponseDTO::class)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida o error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El viaje no está en estado ACEPTADO")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function startTravel(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener ID del path
            $data = ['travelId' => $request->getAttribute('travelId')];

            // 2. Validar
            $validator = new TravelIdValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear DTO
            $dto = new TravelIdDTO(travelId: (int)$data['travelId']);

            // 4. Ejecutar caso de uso
            $result = $this->startAcceptedTravelUseCase->execute($dto);

            // 5. Respuesta
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // PATCH /travels/{travelId}/complete - Finalizar un viaje en curso
    #[OA\Patch(
        path: "/travels/{travelId}/complete",
        operationId: "completeTravel",
        description: "Finaliza un viaje en curso. Solo el conductor asignado puede completar el viaje.",
        summary: "Completar viaje",
        security: [["bearerAuth" => []]],
        tags: ["Travels"]
    )]
    #[OA\Parameter(
        name: "travelId",
        description: "ID del viaje",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Viaje completado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: TravelResponseDTO::class)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida o error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El viaje no está en curso")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function completeTravel(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener ID
            $data = ['travelId' => $request->getAttribute('travelId')];

            // 2. Validar
            $validator = new TravelIdValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear DTO
            $dto = new TravelIdDTO(travelId: (int)$data['travelId']);

            // 4. Ejecutar caso de uso
            $result = $this->completeTravelUseCase->execute($dto);

            // 5. Respuesta
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // PATCH /travels/{travelId}/cancel - Cancelar un viaje
    #[OA\Patch(
        path: "/travels/{travelId}/cancel",
        operationId: "cancelTravel",
        description: "Cancela un viaje. Solo el ciudadano o el conductor asignado pueden cancelar el viaje si está permitido por el estado actual.",
        summary: "Cancelar viaje",
        security: [["bearerAuth" => []]],
        tags: ["Travels"]
    )]
    #[OA\Parameter(
        name: "travelId",
        description: "ID del viaje",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Viaje cancelado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", ref: TravelResponseDTO::class)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida o error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El viaje ya está cancelado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function cancelTravel(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener ID
            $data = ['travelId' => $request->getAttribute('travelId')];

            // 2. Validar
            $validator = new TravelIdValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear DTO
            $dto = new TravelIdDTO(travelId: (int)$data['travelId']);

            // 4. Ejecutar caso de uso
            $result = $this->cancelTravelUseCase->execute($dto);

            // 5. Respuesta
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/admin/travel-statuses",
        operationId: "getTravelStatuses",
        description: "Obtiene estados de viaje con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista estados de viaje con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Travel Status"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre de estado de viaje", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de estados de viaje con paginación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estados de viaje obtenidos exitosamente"),
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
                                    new OA\Property(property: "name", type: "string", example: "En curso"),
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
            $paginationRequest = TravelStatusPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los estados de viaje: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: "/admin/travel-statuses",
        operationId: "createTravelStatus",
        description: "Crea un nuevo estado de viaje.",
        summary: "Crear nuevo estado de viaje.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Travel Status"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "En curso", description: "Nombre del estado de viaje"),
                new OA\Property(property: "active", description: "Estado activo del estado de viaje", type: "boolean", example: true)
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Estado de viaje creado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de viaje creado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "En curso"),
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

            $requestDTO = TravelStatusRequestDTO::fromArray($data);
            $result = $this->createHandler->handle($requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear el estado de viaje: ' . $e->getMessage());
        }
    }

    #[OA\Put(
        path: "/admin/travel-statuses/{id}",
        operationId: "updateTravelStatus",
        description: "Actualiza un estado de viaje existente.",
        summary: "Actualizar estado de viaje.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Travel Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado de viaje", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "En curso modificado", description: "Nombre del estado de viaje"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del estado de viaje")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Estado de viaje actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de viaje actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "En curso modificado"),
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

            $requestDTO = TravelStatusRequestDTO::fromArray($data);
            $result = $this->updateHandler->handle($id, $requestDTO);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error('Datos inválidos: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el estado de viaje: ' . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/admin/travel-statuses/{id}",
        operationId: "deleteTravelStatus",
        description: "Elimina un estado de viaje.",
        summary: "Eliminar estado de viaje.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Travel Status"]
    )]
    #[OA\Parameter(name: "id", description: "ID del estado de viaje", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Estado de viaje eliminado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Estado de viaje eliminado exitosamente")
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
                return $this->error('No se pudo eliminar el estado de viaje. Verifique viajes relacionados.', 400);
            }

            return $this->ok('Estado de viaje eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el estado de viaje: ' . $e->getMessage());
        }
    }
}

