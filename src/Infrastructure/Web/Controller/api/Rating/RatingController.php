<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Rating;

use Exception;
use itaxcix\Core\UseCases\Rating\GetUserRatingsCommentsUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Rating\UserRatingsResponseDto;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RatingController extends AbstractController
{
    private GetUserRatingsCommentsUseCase $getUserRatingsCommentsUseCase;

    public function __construct(GetUserRatingsCommentsUseCase $getUserRatingsCommentsUseCase)
    {
        $this->getUserRatingsCommentsUseCase = $getUserRatingsCommentsUseCase;
    }

    // GET /users/{userId}/ratings/comments - Comentarios recibidos por un usuario
    #[OA\Get(
        path: "/users/{userId}/ratings/comments",
        operationId: "getUserRatingsComments",
        description: "Obtiene todos los comentarios que recibió un usuario en sus viajes junto con su promedio de calificación. Soporta paginación.",
        summary: "Comentarios y calificaciones recibidas por un usuario (paginado)",
        security: [["bearerAuth" => []]],
        tags: ["Ratings", "User"]
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
        description: "Comentarios y promedio de calificación del usuario (paginado)",
        content: new OA\JsonContent(ref: UserRatingsResponseDto::class)
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Usuario no encontrado")
            ],
            type: "object"
        )
    )]
    public function getUserRatingsComments(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int) $request->getAttribute('userId');
            $queryParams = $request->getQueryParams();
            $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
            $perPage = isset($queryParams['perPage']) ? (int)$queryParams['perPage'] : 10;

            $result = $this->getUserRatingsCommentsUseCase->execute($userId, $page, $perPage);
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }
}
