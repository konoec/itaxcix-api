<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Auth;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Auth\LoginUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Auth\AuthLoginRequestDTO;
use itaxcix\Shared\DTO\useCases\Auth\AuthLoginResponseDTO;
use itaxcix\Shared\Validators\useCases\Auth\AuthLoginValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Post(
    path: "/auth/login",
    operationId: "loginUser",
    description: "Autentica al usuario utilizando su documento y contraseña.",
    summary: "Iniciar sesión de usuario",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: AuthLoginRequestDTO::class)
    ),
    tags: ["Auth"]
)]
#[OA\Response(
    response: 200,
    description: "Inicio de sesión exitoso",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: true),
            new OA\Property(property: "message", type: "string", example: "OK"),
            new OA\Property(property: "data", ref: AuthLoginResponseDTO::class),
        ],
        type: "object"
    )
)]
#[OA\Response(
    response: 401,
    description: "Credenciales inválidas",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "No autorizado"),
            new OA\Property(property: "error", properties: [
                new OA\Property(property: "message", type: "string", example: "Credenciales inválidas")
            ], type: "object")
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
                new OA\Property(property: "message", type: "string", example: "Documento o contraseña incorrectos")
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
class AuthController extends AbstractController
{
    private LoginUseCase $loginUseCase;

    public function __construct(LoginUseCase $loginUseCase)
    {
        $this->loginUseCase = $loginUseCase;
    }

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validator = new AuthLoginValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO de entrada
            $loginDto = new AuthLoginRequestDTO(
                documentValue: $data['documentValue'],
                password: $data['password'],
                web: $data['web'] ?? false
            );

            // Usar el UseCase
            $authResponse = $this->loginUseCase->execute($loginDto);

            if (!$authResponse) {
                return $this->error('Credenciales inválidas', 401);
            }

            return $this->ok($authResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}