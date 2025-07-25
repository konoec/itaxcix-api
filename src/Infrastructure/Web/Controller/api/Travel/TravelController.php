<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Travel;

use Exception;
use itaxcix\Core\UseCases\Travel\RateTravelUseCase;
use itaxcix\Core\UseCases\Travel\GetTravelHistoryUseCase;
use itaxcix\Core\UseCases\Travel\GetTravelRatingsByTravelUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Travel\RateTravelRequestDto;
use itaxcix\Shared\DTO\useCases\Travel\TravelHistoryResponseDto;
use itaxcix\Shared\Validators\useCases\Travel\RateTravelValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TravelController extends AbstractController
{
    private RateTravelUseCase $rateTravelUseCase;
    private GetTravelHistoryUseCase $getTravelHistoryUseCase;
    private GetTravelRatingsByTravelUseCase $getTravelRatingsByTravelUseCase;

    public function __construct(
        RateTravelUseCase $rateTravelUseCase,
        GetTravelHistoryUseCase $getTravelHistoryUseCase,
        GetTravelRatingsByTravelUseCase $getTravelRatingsByTravelUseCase
    ) {
        $this->rateTravelUseCase = $rateTravelUseCase;
        $this->getTravelHistoryUseCase = $getTravelHistoryUseCase;
        $this->getTravelRatingsByTravelUseCase = $getTravelRatingsByTravelUseCase;
    }

    // POST /travels/{travelId}/rate - Calificar un viaje
    #[OA\Post(
        path: "/travels/{travelId}/rate",
        operationId: "rateTravel",
        description: "Permite a un usuario calificar un viaje finalizado. Se debe indicar el usuario que califica (raterId).",
        summary: "Calificar un viaje",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RateTravelRequestDto::class)
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
        description: "Calificación registrada exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK")
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
                    new OA\Property(property: "message", type: "string", example: "El viaje no puede ser calificado")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function rateTravel(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $travelId = $request->getAttribute('travelId');
            $data = $this->getJsonBody($request);
            $data['travelId'] = $travelId;

            $validator = new RateTravelValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new RateTravelRequestDto(
                travelId: (int)$data['travelId'],
                raterId: (int)$data['raterId'],
                score: (int)$data['score'],
                comment: $data['comment'] ?? null
            );

            $this->rateTravelUseCase->execute($dto);
            return $this->ok(["success" => true, "message" => "OK"]);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // GET /users/{userId}/travels/history - Historial de viajes por usuario
    #[OA\Get(
        path: "/users/{userId}/travels/history",
        operationId: "getTravelHistory",
        description: "Obtiene el historial de viajes de un usuario con paginación.",
        summary: "Historial de viajes por usuario",
        security: [["bearerAuth" => []]],
        tags: ["Travels", "User"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Parameter(
        name: "page",
        description: "Número de página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", default: 1)
    )]
    #[OA\Parameter(
        name: "perPage",
        description: "Cantidad de elementos por página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", default: 10)
    )]
    #[OA\Response(
        response: 200,
        description: "Historial de viajes paginado",
        content: new OA\JsonContent(ref: TravelHistoryResponseDto::class)
    )]
    public function getTravelHistory(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = $request->getAttribute('userId');
            $queryParams = $request->getQueryParams();
            $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
            $perPage = isset($queryParams['perPage']) ? (int)$queryParams['perPage'] : 10;

            $result = $this->getTravelHistoryUseCase->execute($userId, $page, $perPage);
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // GET /travels/{travelId}/ratings - Historial de calificaciones por viaje
    #[OA\Get(
        path: "/travels/{travelId}/ratings",
        operationId: "getTravelRatingsByTravel",
        description: "Obtiene las calificaciones del conductor y ciudadano para un viaje. Retorna los nombres de los usuarios que califican y son calificados en lugar de sus IDs.",
        summary: "Historial de calificaciones por viaje con nombres de usuarios",
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
        description: "Calificaciones del viaje con nombres de usuarios incluidos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "driverRating",
                    description: "Calificación dada por el conductor",
                    properties: [
                        new OA\Property(property: "id", description: "ID de la calificación", type: "integer", example: 1),
                        new OA\Property(property: "travelId", description: "ID del viaje", type: "integer", example: 123),
                        new OA\Property(property: "raterName", description: "Nombre del usuario que califica", type: "string", example: "Juan Pérez"),
                        new OA\Property(property: "ratedName", description: "Nombre del usuario calificado", type: "string", example: "María García"),
                        new OA\Property(property: "score", description: "Puntaje otorgado (1-5)", type: "integer", example: 5),
                        new OA\Property(property: "comment", description: "Comentario", type: "string", example: "Buen viaje", nullable: true),
                        new OA\Property(property: "createdAt", description: "Fecha de creación", type: "string", example: "2025-06-19T10:00:00Z")
                    ],
                    type: "object",
                    nullable: true
                ),
                new OA\Property(
                    property: "citizenRating",
                    description: "Calificación dada por el ciudadano",
                    properties: [
                        new OA\Property(property: "id", description: "ID de la calificación", type: "integer", example: 2),
                        new OA\Property(property: "travelId", description: "ID del viaje", type: "integer", example: 123),
                        new OA\Property(property: "raterName", description: "Nombre del usuario que califica", type: "string", example: "María García"),
                        new OA\Property(property: "ratedName", description: "Nombre del usuario calificado", type: "string", example: "Juan Pérez"),
                        new OA\Property(property: "score", description: "Puntaje otorgado (1-5)", type: "integer", example: 4),
                        new OA\Property(property: "comment", description: "Comentario", type: "string", example: "Conductor puntual", nullable: true),
                        new OA\Property(property: "createdAt", description: "Fecha de creación", type: "string", example: "2025-06-19T10:05:00Z")
                    ],
                    type: "object",
                    nullable: true
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Travel with ID {travelId} not found.")
            ],
            type: "object"
        )
    )]
    public function getTravelRatingsByTravel(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $travelId = $request->getAttribute('travelId');
            $result = $this->getTravelRatingsByTravelUseCase->execute((int)$travelId);
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }
}
