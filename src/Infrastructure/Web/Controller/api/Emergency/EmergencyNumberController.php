<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Emergency;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberGetUseCase;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberSaveUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Emergency\EmergencyNumberRequestDTO;
use itaxcix\Shared\DTO\useCases\Emergency\EmergencyNumberResponseDTO;
use itaxcix\Shared\Validators\useCases\Emergency\EmergencyNumberValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Get(
    path: "/emergency/number",
    operationId: "getEmergencyNumber",
    description: "Obtiene el número de emergencia configurado.",
    summary: "Obtiene el número de emergencia actual.",
    security: [["bearerAuth" => []]],
    tags: ["Emergency"]
)]
#[OA\Response(
    response: 200,
    description: "Número de emergencia encontrado",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: true),
            new OA\Property(property: "message", type: "string", example: "OK"),
            new OA\Property(property: "data", properties: [
                new OA\Property(property: "number", type: "string", example: "+51999999999")
            ], type: "object")
        ],
        type: "object"
    )
)]
#[OA\Response(
    response: 404,
    description: "Número de emergencia no configurado",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "No encontrado"),
            new OA\Property(property: "error", properties: [
                new OA\Property(property: "message", type: "string", example: "No hay número de emergencia configurado.")
            ], type: "object")
        ],
        type: "object"
    )
)]
class EmergencyNumberController extends AbstractController
{
    private EmergencyNumberGetUseCase $getUseCase;
    private EmergencyNumberSaveUseCase $saveUseCase;

    public function __construct(EmergencyNumberGetUseCase $getUseCase, EmergencyNumberSaveUseCase $saveUseCase)
    {
        $this->getUseCase = $getUseCase;
        $this->saveUseCase = $saveUseCase;
    }

    public function getEmergencyNumber(ServerRequestInterface $request): ResponseInterface
    {
        $number = $this->getUseCase->execute();
        if ($number === null) {
            return $this->error('No hay número de emergencia configurado.', 404);
        }
        $responseDto = new EmergencyNumberResponseDTO($number);
        return $this->ok($responseDto);
    }

    #[OA\Post(
        path: "/emergency/number",
        operationId: "saveEmergencyNumber",
        description: "Guarda o actualiza el número de emergencia.",
        summary: "Guarda o actualiza el número de emergencia.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "number", type: "string", example: "+51999999999")
                ],
                type: "object"
            )
        ),
        tags: ["Emergency"]
    )]
    #[OA\Response(
        response: 200,
        description: "Número de emergencia guardado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK")
            ],
            type: "object"
        )
    )]
    public function saveEmergencyNumber(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new EmergencyNumberValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new EmergencyNumberRequestDTO((string)$data['number']);
            $this->saveUseCase->execute($dto->number);
            return $this->ok(['message' => 'Número de emergencia guardado o configurado correctamente.']);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}