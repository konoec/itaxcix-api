<?php

namespace itaxcix\controllers;

use Exception;
use itaxcix\models\dtos\RegisterCitizenRequest;
use itaxcix\models\dtos\RegisterDriverRequest;
use itaxcix\services\AuthService;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[OA\Tag(name: "Auth", description: "Operaciones relacionadas con autenticación de usuarios")]
class AuthController {

    private AuthService $usuarioService;

    public function __construct(AuthService $usuarioService) {
        $this->usuarioService = $usuarioService;
    }

    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "Iniciar sesión con alias y contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["alias", "password"],
                properties: [
                    new OA\Property(property: "alias", type: "string", example: "juanperez"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Inicio de sesión exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.xxxxx")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciales inválidas")
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: "/api/v1/auth/register/citizen",
        summary: "Registrarse como ciudadano",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["documentType", "documentNumber", "alias", "password", "contactMethod"],
                properties: [
                    new OA\Property(property: "documentType", description: "tb_tipo_documento.tipo_id", type: "integer", example: 1),
                    new OA\Property(property: "documentNumber", description: "Número del documento", type: "string", example: "V12345678"),
                    new OA\Property(property: "alias", type: "string", example: "juanperez"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                    new OA\Property(property: "contactMethod", type: "object", example: ["email" => "juan@example.com"])
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 201, description: "Usuario registrado correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o duplicados")
        ]
    )]
    public function registerCitizen(Request $request, Response $response): Response
    {
        try {
            // Leer cuerpo del request directamente
            $body = $request->getBody()->getContents();
            if (empty(trim($body))) {
                throw new Exception("Cuerpo de solicitud vacío", 400);
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Formato JSON inválido: " . json_last_error_msg(), 400);
            }

            if (!is_array($data)) {
                throw new Exception("El cuerpo debe ser un objeto JSON", 400);
            }

            // Campos requeridos en el JSON
            $requiredFields = ['documentType', 'documentNumber', 'alias', 'password', 'contactMethod'];

            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $data)) {
                    throw new Exception("Campo '$field' es obligatorio", 400);
                }
            }

            // Validar que contactMethod sea un array
            if (!is_array($data['contactMethod'])) {
                throw new Exception("El campo 'contactMethod' debe ser un objeto", 400);
            }

            // Ahora sí puedes crear el DTO sin riesgo de error fatal
            $dto = new RegisterCitizenRequest(
                documentType: $data['documentType'],
                documentNumber: $data['documentNumber'],
                alias: $data['alias'],
                password: $data['password'],
                contactMethod: $data['contactMethod']
            );

            // Registrar ciudadano
            $result = $this->usuarioService->registerCitizen($dto);

            $payload = json_encode($result);
            $response->getBody()->write($payload);
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            // Validar código HTTP
            $code = $e->getCode();
            if (!is_int($code) || $code < 100 || $code > 599) {
                $code = 500;
            }

            $errorResponse = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath(),
                ]
            ];

            $response->getBody()->write(json_encode($errorResponse));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    #[OA\Post(
        path: "/api/v1/auth/register/driver",
        summary: "Registrarse como conductor",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["documentType", "documentNumber", "alias", "password", "contactMethod", "licensePlate"],
                properties: [
                    new OA\Property(property: "documentType", type: "integer", example: 1),
                    new OA\Property(property: "documentNumber", type: "string", example: "V12345678"),
                    new OA\Property(property: "alias", type: "string", example: "carlosdriver"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                    new OA\Property(property: "contactMethod", type: "object", example: ["email" => "carlos@example.com"]),
                    new OA\Property(property: "licensePlate", type: "string", example: "A1B-234D")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Conductor registrado correctamente"
            ),
            new OA\Response(response: 400, description: "Datos inválidos o duplicados")
        ]
    )]
    public function registerDriver(Request $request, Response $response): Response
    {
        try {
            $body = $request->getBody()->getContents();
            if (empty(trim($body))) {
                throw new Exception("Cuerpo de solicitud vacío", 400);
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Formato JSON inválido: " . json_last_error_msg(), 400);
            }

            if (!is_array($data)) {
                throw new Exception("El cuerpo debe ser un objeto JSON", 400);
            }

            $requiredFields = ['documentType', 'documentNumber', 'alias', 'password', 'contactMethod', 'licensePlate'];
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $data)) {
                    throw new Exception("Campo '$field' es obligatorio", 400);
                }
            }

            if (!is_array($data['contactMethod'])) {
                throw new Exception("El campo 'contactMethod' debe ser un objeto", 400);
            }

            $dto = new RegisterDriverRequest(
                documentType: $data['documentType'],
                documentNumber: $data['documentNumber'],
                alias: $data['alias'],
                password: $data['password'],
                contactMethod: $data['contactMethod'],
                licensePlate: $data['licensePlate']
            );

            $result = $this->usuarioService->registerDriver($dto);

            $payload = json_encode($result);
            $response->getBody()->write($payload);
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $code = $e->getCode();
            if (!is_int($code) || $code < 100 || $code > 599) {
                $code = 500;
            }

            $errorResponse = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath(),
                ]
            ];

            $response->getBody()->write(json_encode($errorResponse));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    #[OA\Post(
        path: "/api/v1/auth/recover/email",
        summary: "Solicitar recuperación de contraseña por correo",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Correo de recuperación enviado"
            ),
            new OA\Response(response: 404, description: "Usuario no encontrado")
        ]
    )]
    public function recoverByEmail(): void {}

    #[OA\Post(
        path: "/api/v1/auth/recover/phone",
        summary: "Solicitar recuperación de contraseña por teléfono",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone"],
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "+584121234567")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Código SMS enviado para recuperación"
            ),
            new OA\Response(response: 404, description: "Usuario no encontrado")
        ]
    )]
    public function recoverByPhone(): void {}

    #[OA\Post(
        path: "/api/v1/auth/verify-code",
        summary: "Verificar código de recuperación",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "code", type: "string", example: "123456"),
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+584121234567")
                ],
                oneOf: [
                    new OA\Schema(required: ["email", "code"]),
                    new OA\Schema(required: ["phone", "code"])
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Código verificado correctamente"
            ),
            new OA\Response(response: 400, description: "Código inválido o expirado")
        ]
    )]
    public function verifyCode(): void {}

    #[OA\Post(
        path: "/api/v1/auth/reset-password",
        summary: "Restablecer contraseña tras verificar código",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["userId", "newPassword"],
                properties: [
                    new OA\Property(property: "userId", type: "integer", example: 123),
                    new OA\Property(property: "newPassword", type: "string", format: "password", example: "nueva_contrasena_123")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Contraseña actualizada correctamente"
            ),
            new OA\Response(response: 400, description: "Datos inválidos o token expirado")
        ]
    )]
    public function resetPassword(): void {}
}