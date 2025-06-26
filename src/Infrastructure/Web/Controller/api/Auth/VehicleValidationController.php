<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Auth;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Auth\VehicleValidationValidatorUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Auth\VehicleValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\Auth\VehicleValidationValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Post(
    path: "/auth/validation/vehicle",
    operationId: "validateVehicleWithDocument",
    description: "Valida si el vehículo asociado al documento proporcionado es válido.",
    summary: "Validar vehículo con documento",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: VehicleValidationRequestDTO::class)
    ),
    tags: ["Validation"]
)]
#[OA\Response(
    response: 200,
    description: "Vehículo validado correctamente",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: true),
            new OA\Property(property: "message", type: "string", example: "OK"),
            new OA\Property(
                property: "data",
                description: "Datos asociados al resultado de la validación del vehículo",
                properties: [
                    new OA\Property(property: "personId", type: "integer", example: 123),
                    new OA\Property(property: "vehicleId", type: "integer", example: 456)
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
                    new OA\Property(property: "message", type: "string", example: "Documento o contraseña incorrectos")
                ],
                type: "object"
            )
        ],
        type: "object"
    )
)]
#[OA\Response(
    response: 404,
    description: "Documento o vehículo no encontrado",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "No encontrado"),
            new OA\Property(
                property: "error",
                properties: [
                    new OA\Property(property: "message", type: "string", example: "La persona no existe o no está activa.")
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
class VehicleValidationController extends AbstractController {

    private VehicleValidationValidatorUseCase $vehicleValidationValidatorUseCase;

    public function __construct(VehicleValidationValidatorUseCase $vehicleValidationValidatorUseCase) {
        $this->vehicleValidationValidatorUseCase = $vehicleValidationValidatorUseCase;
    }

    public function validateVehicleWithDocument(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new VehicleValidationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new VehicleValidationRequestDTO(
                documentTypeId: (int) $data['documentTypeId'],
                documentValue: (string) $data['documentValue'],
                plateValue: (string) $data['plateValue']
            );

            // 4. Llamar a lógica de validación (aquí iría el caso de uso)
            $result = $this->vehicleValidationValidatorUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}