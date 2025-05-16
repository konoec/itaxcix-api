<?php

namespace itaxcix\controllers;

use Exception;
use itaxcix\models\dtos\AttachContactRequest;
use itaxcix\models\dtos\AttachVehicleRequest;
use itaxcix\models\dtos\DetachContactRequest;
use itaxcix\models\dtos\DetachVehicleRequest;
use itaxcix\models\dtos\SendVerificationCodeRequest;
use itaxcix\models\dtos\VerifyCodeRequest;
use itaxcix\services\PerfilService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Perfil", description: "Operaciones relacionadas con el perfil del usuario")]
class PerfilController extends BaseController {
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
    public function sendVerificationCode(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new SendVerificationCodeRequest($data);

            $this->perfilService->sendVerificationCode($dto);

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
    public function verifyContactCode(Request $request, Response $response): Response {
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

    #[OA\Post(
        path: "/perfil/detach-vehicle",
        summary: "Desvincular un vehículo del perfil del conductor",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: DetachVehicleRequest::class)
        ),
        tags: ["Perfil"],
        responses: [
            new OA\Response(response: 200, description: "Vehículo desvinculado correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o sin vehículo asignado"),
            new OA\Response(response: 404, description: "Conductor o vehículo no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function detachVehicle(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new DetachVehicleRequest($data);

            $this->perfilService->detachVehicle($dto->userId, $dto->vehicleId);

            return $this->respondWithJson($response, ['message' => 'Vehículo desvinculado correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/perfil/attach-vehicle",
        summary: "Vincular un vehículo al perfil del conductor",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: AttachVehicleRequest::class)
        ),
        tags: ["Perfil"],
        responses: [
            new OA\Response(response: 200, description: "Vehículo vinculado correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o vehículo ya vinculado"),
            new OA\Response(response: 404, description: "Conductor o vehículo no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function attachVehicle(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new AttachVehicleRequest($data);

            $this->perfilService->attachVehicle($dto->userId, $dto->vehicleId);

            return $this->respondWithJson($response, ['message' => 'Vehículo vinculado correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/perfil/attach-contact",
        summary: "Vincular un nuevo contacto (correo o teléfono) al perfil",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: AttachContactRequest::class)
        ),
        tags: ["Perfil"],
        responses: [
            new OA\Response(response: 200, description: "Contacto vinculado correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos o contacto duplicado"),
            new OA\Response(response: 404, description: "Usuario no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function attachContact(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new AttachContactRequest($data);

            $this->perfilService->attachContact($dto->userId, $dto->contact, $dto->contactTypeId);

            return $this->respondWithJson($response, ['message' => 'Contacto vinculado correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }

    #[OA\Post(
        path: "/perfil/detach-contact",
        summary: "Desvincular un contacto del perfil del usuario",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: DetachContactRequest::class)
        ),
        tags: ["Perfil"],
        responses: [
            new OA\Response(response: 200, description: "Contacto desvinculado correctamente"),
            new OA\Response(response: 400, description: "Datos inválidos"),
            new OA\Response(response: 404, description: "Usuario o contacto no encontrado"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function detachContact(Request $request, Response $response): Response {
        try {
            $data = $this->getJsonObject($request);
            $dto = new DetachContactRequest($data);

            $this->perfilService->detachContact($dto->userId, $dto->contact);

            return $this->respondWithJson($response, ['message' => 'Contacto desvinculado correctamente']);
        } catch (Exception $e) {
            return $this->handleError($e, $request, $response);
        }
    }
}
