<?php

namespace itaxcix\controllers;

use Exception;
use itaxcix\models\dtos\SendVerificationCodeRequest;
use itaxcix\models\dtos\VerifyCodeRequest;
use itaxcix\services\PerfilService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Perfil", description: "Operaciones relacionadas con el perfil del usuario")]
class PerfilController extends BaseController
{
    public function __construct(private readonly PerfilService $perfilService) {}

    #[OA\Post(
        path: "/perfil/send-verification-code",
        summary: "Enviar código de verificación a un contacto",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: SendVerificationCodeRequest::class)
        ),
        tags: ["Perfil"],
        responses: [
            new OA\Response(response: 200, description: "Código enviado exitosamente"),
            new OA\Response(response: 400, description: "Datos inválidos o contacto ya verificado"),
            new OA\Response(response: 404, description: "Contacto no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function sendVerificationCode(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonObject($request);
            $dto = new SendVerificationCodeRequest($data);

            $this->perfilService->sendVerificationCode($dto->contactTypeId, $dto->contact);

            return $this->respondWithJson($response, ['message' => 'Código de verificación enviado']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/perfil/verify-contact-code",
        summary: "Verificar contacto mediante código",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: VerifyCodeRequest::class)
        ),
        tags: ["Perfil"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Contacto verificado correctamente",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                    new OA\Property(property: "userId", type: "integer")
                ])
            ),
            new OA\Response(response: 400, description: "Código inválido o datos incorrectos"),
            new OA\Response(response: 404, description: "Contacto no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function verifyContactCode(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonObject($request);
            $dto = new VerifyCodeRequest($data);

            $result = $this->perfilService->verifyContactCode($dto->code, $dto->contactTypeId, $dto->contact);

            return $this->respondWithJson($response, [
                'message' => 'Contacto verificado correctamente',
                'userId' => $result['userId']
            ]);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }
}
