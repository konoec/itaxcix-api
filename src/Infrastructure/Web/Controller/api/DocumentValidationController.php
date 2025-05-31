<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\DocumentValidationUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\DocumentValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\DocumentValidationValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/auth/validation/document",
    operationId: "validateDocument",
    description: "Recibe un tipo de documento y su valor para realizar una validación lógica y de formato.",
    summary: "Valida un documento según su tipo y valor",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: DocumentValidationRequestDTO::class)
    ),
    tags: ["Auth", "Validation"]
)]
#[OA\Response(
    response: 200,
    description: "Documento validado correctamente",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: true),
            new OA\Property(property: "message", type: "string", example: "OK"),
            new OA\Property(
                property: "data",
                properties: [
                    new OA\Property(property: "personId", type: "integer", example: 123)
                ],
                type: "object"
            ),
            new OA\Property(property: "error", type: "null")
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
                new OA\Property(property: "message", type: "string", example: "El tipo de documento no es admitido actualmente.")
            ], type: "object")
        ],
        type: "object"
    )
)]
#[OA\Response(
    response: 404,
    description: "Documento no encontrado",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "No encontrado"),
            new OA\Property(property: "error", properties: [
                new OA\Property(property: "message", type: "string", example: "El documento no existe.")
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
class DocumentValidationController extends AbstractController {

    private DocumentValidationUseCase $documentValidationUseCase;

    public function __construct(DocumentValidationUseCase $documentValidationUseCase)
    {
        $this->documentValidationUseCase = $documentValidationUseCase;
    }

    public function validateDocument(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new DocumentValidationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new DocumentValidationRequestDTO(
                documentTypeId: (int) $data['documentTypeId'],
                documentValue: (string) $data['documentValue']
            );

            // 4. Lógica de validación
            $result = $this->documentValidationUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}