<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Travel;

use Exception;
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
use itaxcix\Shared\Validators\useCases\Travel\RequestTravelValidator;
use itaxcix\Shared\Validators\useCases\Travel\RespondToRequestValidator;
use itaxcix\Shared\Validators\useCases\Travel\TravelIdValidator;
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

    private function __construct(
        CancelTravelUseCase $cancelTravelUseCase,
        CompleteTravelUseCase $completeTravelUseCase,
        RespondToTravelRequestUseCase $respondToTravelRequestUseCase,
        StartAcceptedTravelUseCase $startAcceptedTravelUseCase,
        RequestNewTravelUseCase $requestNewTravelUseCase
    ) {
        $this->cancelTravelUseCase = $cancelTravelUseCase;
        $this->completeTravelUseCase = $completeTravelUseCase;
        $this->respondToTravelRequestUseCase = $respondToTravelRequestUseCase;
        $this->startAcceptedTravelUseCase = $startAcceptedTravelUseCase;
        $this->requestNewTravelUseCase = $requestNewTravelUseCase;
    }

    // POST /travels - Solicitar un nuevo viaje
    #[OA\Post(
        path: "/travels",
        operationId: "requestTravel",
        description: "Solicitar un nuevo viaje",
        summary: "Crear una nueva solicitud de viaje",
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
                new OA\Property(property: "data", ref: TravelResponseDTO::class),
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Errores de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Datos de viaje incorrectos")
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
        description: "El conductor acepta o rechaza una solicitud de viaje",
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
                new OA\Property(property: "data", ref: TravelResponseDTO::class),
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Errores de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "Datos incorrectos")
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
        description: "Iniciar un viaje previamente aceptado",
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
                new OA\Property(property: "data", ref: TravelResponseDTO::class),
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Viaje no encontrado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "No encontrado"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El viaje no existe o no está disponible")
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
        description: "Finalizar un viaje en curso",
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
                new OA\Property(property: "data", ref: TravelResponseDTO::class),
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Estado incorrecto",
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
        description: "Cancelar un viaje",
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
                new OA\Property(property: "data", ref: TravelResponseDTO::class),
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Cancelación no permitida",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "No se puede cancelar el viaje en este estado")
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
}