<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\ChangePasswordUseCase;
use itaxcix\Core\UseCases\StartPasswordRecoveryUseCase;
use itaxcix\Core\UseCases\VerifyRecoveryCodeUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\PasswordChangeRequestDTO;
use itaxcix\Shared\DTO\useCases\RecoveryStartRequestDTO;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;
use itaxcix\Shared\Validators\useCases\PasswordChangeValidator;
use itaxcix\Shared\Validators\useCases\RecoveryStartValidator;
use itaxcix\Shared\Validators\useCases\VerificationCodeValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Attributes as OA;

class RecoveryController extends AbstractController {
    private StartPasswordRecoveryUseCase $startPasswordRecoveryUseCase;
    private VerifyRecoveryCodeUseCase $verifyRecoveryCodeUseCase;
    private ChangePasswordUseCase $changePasswordUseCase;

    public function __construct(StartPasswordRecoveryUseCase $startPasswordRecoveryUseCase, ChangePasswordUseCase $changePasswordUseCase, VerifyRecoveryCodeUseCase $verificationCodeUseCase)
    {
        $this->startPasswordRecoveryUseCase = $startPasswordRecoveryUseCase;
        $this->changePasswordUseCase = $changePasswordUseCase;
        $this->verifyRecoveryCodeUseCase = $verificationCodeUseCase;
    }

    #[OA\Post(
        path: "/auth/recovery/start",
        operationId: "startPasswordRecovery",
        description: "Envía un código de recuperación al contacto especificado.",
        summary: "Iniciar recuperación de contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RecoveryStartRequestDTO::class)
        ),
        tags: ["Auth", "Recovery"]
    )]
    #[OA\Response(
        response: 200,
        description: "Código de recuperación enviado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    description: "Datos asociados a la recuperación",
                    properties: [
                        new OA\Property(property: "userId", type: "integer", example: 123)
                    ],
                    type: "object"
                )
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
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El contacto no está activo.")
                    ],
                    type: "object"
                )
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
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function startPasswordRecovery(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new RecoveryStartValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new RecoveryStartRequestDTO(
                contactTypeId: (int) $data['contactTypeId'],
                contactValue: (string) $data['contactValue']
            );

            // 4. Simular lógica de inicio de recuperación
            $result = $this->startPasswordRecoveryUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/auth/recovery/verify-code",
        operationId: "verifyRecoveryCode",
        description: "Verifica si el código proporcionado es válido para el usuario.",
        summary: "Verificar código de recuperación",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: VerificationCodeRequestDTO::class)
        ),
        tags: ["Auth", "Recovery"]
    )]
    #[OA\Response(
        response: 200,
        description: "Código verificado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El código de recuperación es válido."),
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.xxxxx")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Código inválido o expirado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El código de recuperación no es válido.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Errores de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "userId es requerido")
                    ],
                    type: "object"
                )
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
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function verifyRecoveryCode(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar campos requeridos
            $validator = new VerificationCodeValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new VerificationCodeRequestDTO(
                userId: (int) $data['userId'],
                code: (string) $data['code']
            );

            // 4. Simular lógica de verificación de código
            $result = $this->verifyRecoveryCodeUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/auth/recovery/change-password",
        operationId: "changePassword",
        description: "Permite cambiar la contraseña una vez verificado el código de recuperación.",
        summary: "Cambiar contraseña",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: PasswordChangeRequestDTO::class)
        ),
        tags: ["Auth", "Recovery"]
    )]
    #[OA\Response(
        response: 200,
        description: "Contraseña cambiada correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Contraseña cambiada exitosamente")
                    ],
                    type: "object"
                )
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
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Las contraseñas no coinciden.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 403,
        description: "Usuario no autorizado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "No autorizado"),
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Usuario no autorizado")
                    ],
                    type: "object"
                )
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
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function changePassword(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener y validar body
            $data = $this->getJsonBody($request);
            $validator = new PasswordChangeValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->error(reset($errors), 400);
            }

            // 2. Mapear DTO
            $dto = new PasswordChangeRequestDTO(
                userId: (int) $data['userId'],
                newPassword: (string) $data['newPassword'],
                repeatPassword: (string) $data['repeatPassword']
            );

            // 3. Validar coincidencia con JWT
            $authUser = $request->getAttribute('user');
            if (!isset($authUser['userId']) || $dto->userId !== $authUser['userId']) {
                return $this->error('Usuario no autorizado', 403);
            }

            // 4. Ejecutar caso de uso de cambio de contraseña
            $result = $this->changePasswordUseCase->execute($dto);

            // 5. Responder con éxito
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}