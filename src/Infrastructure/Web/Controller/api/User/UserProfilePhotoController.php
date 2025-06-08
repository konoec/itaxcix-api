<?php

namespace itaxcix\Infrastructure\Web\Controller\api\User;

use InvalidArgumentException;
use itaxcix\Core\UseCases\User\UserProfilePhotoUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserProfilePhotoController extends AbstractController
{
    private UserProfilePhotoUseCase $userProfilePhotoUseCase;

    public function __construct(UserProfilePhotoUseCase $userProfilePhotoUseCase)
    {
        $this->userProfilePhotoUseCase = $userProfilePhotoUseCase;
    }

    #[OA\Get(
        path: "/users/{id}/profile-photo",
        operationId: "getUserProfilePhoto",
        description: "Obtiene la foto de perfil del usuario en formato base64.",
        summary: "Obtener foto de perfil del usuario",
        security: [["bearerAuth" => []]],
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID del usuario",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 123)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Foto obtenida correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "userId", type: "integer", example: 123),
                            new OA\Property(property: "base64Image", type: "string", example: "R0lGODlhAQABAIAAAAUEBA...")
                        ], type: "object")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Foto de perfil no encontrada o usuario no existe",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Foto de perfil no encontrada")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function getProfilePhoto(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int) $request->getAttribute('id');

            if ($userId <= 0) {
                return $this->error('ID de usuario inválido', 400);
            }

            // Ejecutar el caso de uso
            $response = $this->userProfilePhotoUseCase->execute($userId);

            return $this->ok($response);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}