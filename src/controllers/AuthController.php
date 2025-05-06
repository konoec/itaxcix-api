<?php

namespace itaxcix\controllers;

use Exception;
use itaxcix\models\dtos\LoginRequest;
use itaxcix\models\dtos\RegisterCitizenRequest;
use itaxcix\models\dtos\RegisterDriverRequest;
use itaxcix\models\dtos\ResetPasswordRequest;
use itaxcix\models\dtos\VerifyCodeRequest;
use itaxcix\services\AuthService;
use Nyholm\Psr7\Stream;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;

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
                        new OA\Property(property: "message", type: "string", example: "Login successful")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciales inválidas")
        ]
    )]
    public function login(Request $request, Response $response): Response
    {
        try {
            // 1. Leer y validar cuerpo de la solicitud
            $body = $request->getBody()->getContents();
            if (empty(trim($body))) {
                throw new Exception("Cuerpo de solicitud vacío", 400);
            }

            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Formato JSON inválido: " . json_last_error_msg(), 400);
            }

            // 2. Validar campos requeridos
            if (!isset($data['alias']) || !isset($data['password'])) {
                throw new Exception("Faltan campos requeridos (alias y password)", 400);
            }

            // 3. Crear DTO (ya incluye validación)
            $dto = new LoginRequest($data['alias'], $data['password']);

            // 4. Llamar al servicio de autenticación
            $result = $this->usuarioService->login($dto);

            // 5. Enviar respuesta exitosa
            $payload = json_encode([
                'message' => 'Login successful',
                'user' => $result
            ]);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream($payload));

        } catch (Exception $e) {
            // Manejar errores
            $code = $this->getStatusCodeFromException($e);
            $errorResponse = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath(),
                ]
            ];

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream(json_encode($errorResponse)));
        }
    }

    // Método auxiliar para crear stream
    private function createStream(string $content): StreamInterface
    {
        $stream = new Stream(fopen('php://memory', 'r+'));
        $stream->write($content);
        $stream->rewind();
        return $stream;
    }

    private function getStatusCodeFromException(Exception $e): int
    {
        $code = $e->getCode();
        return is_int($code) && $code >= 100 && $code <= 599 ? $code : 500;
    }

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
            new OA\Response(response: 200, description: "Correo de recuperación enviado"),
            new OA\Response(response: 404, description: "Usuario no encontrado")
        ]
    )]
    public function recoverByEmail(Request $request, Response $response): Response
    {
        try {
            $body = json_decode($request->getBody()->getContents(), true);
            if (!isset($body['email']) || !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Correo electrónico inválido.", 400);
            }

            $this->usuarioService->requestRecoveryByEmail($body['email']);

            $payload = json_encode(['message' => 'Correo de recuperación enviado']);
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream($payload));

        } catch (Exception $e) {
            $code = $this->getStatusCodeFromException($e);
            $errorResponse = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath()
                ]
            ];
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream(json_encode($errorResponse)));
        }
    }

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
            new OA\Response(response: 200, description: "Código SMS enviado"),
            new OA\Response(response: 404, description: "Usuario no encontrado")
        ]
    )]
    public function recoverByPhone(Request $request, Response $response): Response
    {
        try {
            // 1. Leer cuerpo del request
            $body = json_decode($request->getBody()->getContents(), true);
            if (!isset($body['phone'])) {
                throw new Exception("Número de teléfono requerido", 400);
            }

            $phone = trim($body['phone']);

            // 2. Validar formato del teléfono (ajusta según tu país)
            if (!preg_match('/^\+?[0-9]{8,15}$/', $phone)) {
                throw new Exception("Número de teléfono inválido. Debe tener entre 8 y 15 dígitos.", 400);
            }

            // 3. Llamar al servicio para solicitar recuperación por teléfono
            $this->usuarioService->requestRecoveryByPhone($phone);

            // 4. Responder exitosamente
            $payload = json_encode(['message' => 'Código SMS enviado']);
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream($payload));

        } catch (Exception $e) {
            // Manejar errores
            $code = $this->getStatusCodeFromException($e);
            $errorResponse = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath()
                ]
            ];
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream(json_encode($errorResponse)));
        }
    }

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
            new OA\Response(response: 200, description: "Código verificado correctamente"),
            new OA\Response(response: 400, description: "Código inválido o expirado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function verifyCode(Request $request, Response $response): Response
    {
        try {
            // Leer cuerpo de la solicitud
            $body = $request->getBody()->getContents();
            if (empty(trim($body))) {
                throw new \Exception("Cuerpo de la solicitud vacío", 400);
            }

            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Formato JSON inválido: " . json_last_error_msg(), 400);
            }

            // Validar datos con DTO
            $dto = new VerifyCodeRequest(
                code: $data['code'] ?? '',
                email: $data['email'] ?? null,
                phone: $data['phone'] ?? null
            );

            // Llamar al servicio
            $result = $this->usuarioService->verifyCode($dto->code, $dto->email, $dto->phone);

            // Responder exitosamente
            $payload = json_encode([
                'message' => 'Código verificado exitosamente',
                'userId' => $result['userId']
            ]);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream($payload));

        } catch (\Throwable $th) {
            // Capturar cualquier error inesperado
            $code = $th instanceof \Exception ? $this->getStatusCodeFromException($th) : 500;
            $errorMessage = $th->getMessage();

            // Crear respuesta de error genérica
            $errorResponse = [
                'error' => [
                    'message' => $errorMessage,
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath()
                ]
            ];

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream(json_encode($errorResponse)));
        }
    }

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
            new OA\Response(response: 200, description: "Contraseña restablecida correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o token expirado")
        ]
    )]
    public function resetPassword(Request $request, Response $response): Response
    {
        try {
            // Decodificar cuerpo de la solicitud
            $body = json_decode($request->getBody()->getContents(), true);

            if (!isset($body['userId']) || !is_int($body['userId'])) {
                throw new \Exception("ID de usuario inválido", 400);
            }

            if (!isset($body['newPassword']) || !is_string($body['newPassword'])) {
                throw new \Exception("Contraseña inválida", 400);
            }

            // Llamar al servicio
            $this->usuarioService->resetPassword($body['userId'], $body['newPassword']);

            // Devolver éxito
            $payload = json_encode(['message' => 'Contraseña restablecida correctamente']);
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream($payload));

        } catch (\Throwable $th) {
            // Manejar errores y siempre devolver una respuesta válida
            $code = $this->getStatusCodeFromException($th);
            $errorResponse = [
                'error' => [
                    'message' => $th->getMessage(),
                    'code' => $code,
                    'status' => $code,
                    'timestamp' => date('c'),
                    'path' => $request->getUri()->getPath()
                ]
            ];

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream(json_encode($errorResponse)));
        }
    }
}