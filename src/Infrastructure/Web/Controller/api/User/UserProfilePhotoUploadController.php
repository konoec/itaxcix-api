<?php

namespace itaxcix\Infrastructure\Web\Controller\api\User;

use InvalidArgumentException;
use itaxcix\Core\UseCases\User\UserProfilePhotoUploadUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\User\UserProfilePhotoUploadRequestDTO;
use itaxcix\Shared\Validators\useCases\User\UserProfilePhotoUploadValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserProfilePhotoUploadController extends AbstractController
{
    private UserProfilePhotoUploadUseCase $userProfilePhotoUploadUseCase;

    public function __construct(UserProfilePhotoUploadUseCase $userProfilePhotoUploadUseCase)
    {
        $this->userProfilePhotoUploadUseCase = $userProfilePhotoUploadUseCase;
    }

    #[OA\Post(
        path: "/users/{id}/profile-photo",
        operationId: "uploadUserProfilePhoto",
        description: "Sube una foto de perfil para el usuario en formato base64.",
        summary: "Subir foto de perfil del usuario",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["base64Image"],
                properties: [
                    new OA\Property(property: "base64Image", description: "Imagen en formato base64 (data:image/jpeg;base64,...)", type: "string")
                ]
            )
        ),
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
                description: "Foto subida correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Foto de perfil actualizada correctamente")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "Error al procesar la imagen",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al procesar la imagen")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Usuario no encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Usuario no encontrado")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function uploadProfilePhoto(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener datos
            $userId = (int) $request->getAttribute('id');
            $data = $this->getJsonBody($request);
            $data['userId'] = $userId;

            // 2. Validar datos
            $validator = new UserProfilePhotoUploadValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Crear el DTO de solicitud
            $requestDTO = new UserProfilePhotoUploadRequestDTO(
                $userId,
                $data['base64Image']
            );

            // 4. Ejecutar el caso de uso
            $response = $this->userProfilePhotoUploadUseCase->execute($requestDTO);

            // 5. Devolver resultado exitoso
            return $this->ok($response);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}