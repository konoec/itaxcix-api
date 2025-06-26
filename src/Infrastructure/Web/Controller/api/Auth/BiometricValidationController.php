<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Auth;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Auth\BiometricValidationUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Auth\BiometricValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\Auth\BiometricValidationValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Post(
    path: "/auth/validation/biometric",
    operationId: "validateBiometric",
    description: "Recibe una imagen en formato Base64 y un ID de persona para validar si corresponde.",
    summary: "Valida una imagen biométrica de una persona registrada.",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: BiometricValidationRequestDTO::class)
    ),
    tags: ["Validation"]
)]
#[OA\Response(
    response: 200,
    description: "Imagen biométrica válida",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: true),
            new OA\Property(property: "message", type: "string", example: "OK"),
            new OA\Property(
                property: "data",
                properties: [
                    new OA\Property(property: "personId", type: "integer", example: 123),
                    new OA\Property(property: "validationDate", type: "string", format: "date-time", example: "2025-04-05T10:00:00Z")
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
            new OA\Property(property: "error", properties: [
                new OA\Property(property: "message", type: "string", example: "No se detectó un rostro válido en la imagen.")
            ], type: "object")
        ],
        type: "object"
    )
)]
#[OA\Response(
    response: 404,
    description: "Persona no encontrada",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "No encontrado"),
            new OA\Property(property: "error", properties: [
                new OA\Property(property: "message", type: "string", example: "La persona no existe o no está activa.")
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
                new OA\Property(property: "message", type: "string", example: "Error al detectar el rostro")
            ], type: "object")
        ],
        type: "object"
    )
)]
class BiometricValidationController extends AbstractController {

    private BiometricValidationUseCase $biometricValidationUseCase;

    public function __construct(BiometricValidationUseCase $biometricValidationUseCase) {
        $this->biometricValidationUseCase = $biometricValidationUseCase;
    }

    public function validateBiometric(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar campos requeridos
            $validator = new BiometricValidationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear al DTO de entrada
            $dto = new BiometricValidationRequestDTO(
                personId: (int) $data['personId'],
                imageBase64: (string) $data['imageBase64']
            );

            // 4. Lógica de validación
            $result = $this->biometricValidationUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}