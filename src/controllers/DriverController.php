<?php

namespace itaxcix\controllers;

use Exception;
use itaxcix\models\dtos\ActivateAvailabilityRequest;
use itaxcix\models\dtos\DeactivateAvailabilityRequest;
use itaxcix\services\DriverService;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Conductor", description: "Operaciones relacionadas con el conductor")]
class DriverController extends BaseController {

    private DriverService $driverService;

    public function __construct(DriverService $driverService) {
        $this->driverService = $driverService;
    }

    #[OA\Post(
        path: "/driver/activate-availability",
        summary: "Activar disponibilidad del conductor para recibir viajes",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: ActivateAvailabilityRequest::class)
        ),
        tags: ["Conductor"],
        responses: [
            new OA\Response(response: 200, description: "Disponibilidad activada correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o conductor no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function activateAvailability(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new ActivateAvailabilityRequest($data);

            $this->driverService->activateAvailability($dto->userId);

            return $this->respondWithJson($response, ['message' => 'Disponibilidad activada correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/driver/deactivate-availability",
        summary: "Desactivar disponibilidad del conductor",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: DeactivateAvailabilityRequest::class)
        ),
        tags: ["Conductor"],
        responses: [
            new OA\Response(response: 200, description: "Disponibilidad desactivada correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o conductor no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function deactivateAvailability(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new DeactivateAvailabilityRequest($data);

            $this->driverService->deactivateAvailability($dto->userId);

            return $this->respondWithJson($response, ['message' => 'Disponibilidad desactivada correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Get(
        path: "/driver/status/{userId}",
        summary: "Obtener el estado actual de disponibilidad del conductor",
        security: [["bearerAuth" => []]],
        tags: ["Conductor"],
        parameters: [
            new OA\Parameter(
                name: "userId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estado del conductor obtenido exitosamente",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "available", type: "boolean"),
                    new OA\Property(property: "lastUpdated", type: "string", format: "date-time")
                ])
            ),
            new OA\Response(response: 404, description: "Conductor no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function getDriverStatus(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['userId'];

            $status = $this->driverService->getDriverStatus($userId);

            return $this->respondWithJson($response, [
                'available' => $status['available'],
                'lastUpdated' => $status['updatedAt']
            ]);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }
}