<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\UserRegistrationUseCase;
use itaxcix\Core\UseCases\VerificationCodeUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\RegistrationRequestDTO;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;
use itaxcix\Shared\Validators\useCases\RegistrationValidator;
use itaxcix\Shared\Validators\useCases\VerificationCodeValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Attributes as OA;

class RegistrationController extends AbstractController {
    private UserRegistrationUseCase $userRegistrationUseCase;
    private VerificationCodeUseCase $verificationCodeUseCase;
    public function __construct(UserRegistrationUseCase $userRegistrationUseCase, VerificationCodeUseCase $verificationCodeUseCase)
    {
        $this->userRegistrationUseCase = $userRegistrationUseCase;
        $this->verificationCodeUseCase = $verificationCodeUseCase;
    }

    #[OA\Post(
        path: "/auth/registration",
        operationId: "submitRegistrationData",
        description: "Recibe los datos necesarios para registrar a un nuevo usuario.",
        summary: "Registrar nuevo usuario",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RegistrationRequestDTO::class)
        ),
        tags: ["Auth", "Registration"]
    )]
    #[OA\Response(
        response: 201,
        description: "Usuario registrado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Creado"),
                new OA\Property(
                    property: "data",
                    description: "Datos del usuario creado",
                    properties: [
                        new OA\Property(property: "userId", type: "integer", example: 123)
                    ]
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
                        new OA\Property(property: "message", type: "string", example: "Ya existe un contacto registrado con ese número o correo.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Usuario ya registrado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ya existe un usuario registrado con esa persona.")
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
    public function submitRegistrationData(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new RegistrationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new RegistrationRequestDTO(
                password: (string) $data['password'],
                contactTypeId: (int) $data['contactTypeId'],
                contactValue: (string) $data['contactValue'],
                personId: (int) $data['personId'],
                vehicleId: isset($data['vehicleId']) ? (int) $data['vehicleId'] : null
            );

            // 4. Lógica de registro
            $result = $this->userRegistrationUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->created($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/auth/registration/verify-code",
        operationId: "verifyContactCode",
        description: "Verifica si el código proporcionado es válido para el usuario.",
        summary: "Verificar código de contacto",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: VerificationCodeRequestDTO::class)
        ),
        tags: ["Auth", "Registration"]
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
                        new OA\Property(property: "message", type: "string", example: "El código de verificación es válido.")
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
                        new OA\Property(property: "message", type: "string", example: "El código de verificación no es válido.")
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
    public function verifyContactCode(ServerRequestInterface $request): ResponseInterface {
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

            // 4. Lógica de verificación
            $result = $this->verificationCodeUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}