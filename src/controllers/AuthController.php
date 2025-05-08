<?php

namespace itaxcix\controllers;

use Exception;
use itaxcix\models\dtos\LoginRequest;
use itaxcix\models\dtos\RecoveryRequest;
use itaxcix\models\dtos\RegisterCitizenRequest;
use itaxcix\models\dtos\RegisterDriverRequest;
use itaxcix\models\dtos\ResetPasswordRequest;
use itaxcix\models\dtos\VerifyCodeRequest;
use itaxcix\services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Authentication", description: "Endpoints relacionados con autenticación")]
class AuthController extends BaseController {

    private AuthService $usuarioService;

    public function __construct(AuthService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    #[OA\Post(
        path: "/auth/login",
        operationId: "login",
        summary: "Iniciar sesión",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: LoginRequest::class)
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Inicio de sesión exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciales inválidas"),
            new OA\Response(response: 400, description: "Datos inválidos")
        ]
    )]
    /**
     * Inicia sesión en la aplicación.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new LoginRequest($data);
            $result = $this->usuarioService->login($dto);

            return $this->respondWithJson($response, [
                'message' => 'Login successful',
                'user' => $result
            ]);

        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/auth/register/citizen",
        operationId: "registerCitizen",
        summary: "Registrar un ciudadano",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RegisterCitizenRequest::class)
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Registro exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "userId", type: "integer"),
                        new OA\Property(property: "personId", type: "integer")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Datos inválidos"),
            new OA\Response(response: 422, description: "No se encontraron datos en la API externa"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    /**
     * Registra un nuevo ciudadano en la aplicación.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function registerCitizen(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new RegisterCitizenRequest($data);
            $result = $this->usuarioService->registerCitizen($dto);

            return $this->respondWithJson($response, $result, 201);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/auth/register/driver",
        operationId: "registerDriver",
        summary: "Registrar un conductor",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RegisterDriverRequest::class)
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Registro exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "userId", type: "integer"),
                        new OA\Property(property: "personId", type: "integer"),
                        new OA\Property(property: "vehicleId", type: "integer")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Datos inválidos"),
            new OA\Response(response: 422, description: "No se encontraron datos en la API externa"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    /**
     * Registra un nuevo conductor en la aplicación.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function registerDriver(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new RegisterDriverRequest($data);
            $result = $this->usuarioService->registerDriver($dto);

            return $this->respondWithJson($response, $result, 201);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/auth/recovery",
        operationId: "requestRecovery",
        summary: "Solicitar recuperación de contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RecoveryRequest::class)
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Solicitud de recuperación procesada",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Datos inválidos"),
            new OA\Response(response: 404, description: "Contacto no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    /**
     * Solicita la recuperación de la contraseña.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function requestRecovery(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new RecoveryRequest($data);

            if ($dto->contactTypeId === 1) {
                $this->usuarioService->requestRecoveryByEmail($dto->contact);
            } else {
                $this->usuarioService->requestRecoveryByPhone($dto->contact);
            }

            return $this->respondWithJson($response, ['message' => 'Solicitud de recuperación procesada']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/auth/verify-code",
        operationId: "verifyCode",
        summary: "Verificar el código de recuperación",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: VerifyCodeRequest::class)
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Código verificado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "userId", type: "integer")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Código inválido o datos incorrectos"),
            new OA\Response(response: 404, description: "Contacto no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    /**
     * Verifica el código de recuperación de contraseña.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function verifyCode(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new VerifyCodeRequest($data);
            $result = $this->usuarioService->verifyCode($dto->code, $dto->contactTypeId, $dto->contact);

            return $this->respondWithJson($response, [
                'message' => 'Código verificado exitosamente',
                'userId' => $result['userId']
            ]);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/auth/reset-password",
        operationId: "resetPassword",
        summary: "Restablecer la contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: ResetPasswordRequest::class)
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Contraseña restablecida correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Datos inválidos"),
            new OA\Response(response: 404, description: "Usuario no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    /**
     * Restablece la contraseña del usuario.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function resetPassword(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonBody($request);
            $dto = new ResetPasswordRequest($data);
            $this->usuarioService->resetPassword($dto->userId, $dto->newPassword);

            return $this->respondWithJson($response, ['message' => 'Contraseña restablecida correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }
}