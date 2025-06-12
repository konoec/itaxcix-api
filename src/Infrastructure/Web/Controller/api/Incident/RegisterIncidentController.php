<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Incident;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Incident\RegisterIncidentUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Incident\RegisterIncidentRequestDTO;
use itaxcix\Shared\DTO\useCases\Incident\RegisterIncidentResponseDTO;
use itaxcix\Shared\Validators\useCases\Incident\RegisterIncidentValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Post(
    path: "/incidents/register",
    operationId: "registerIncident",
    description: "Registra un nuevo incidente asociado a un viaje y usuario.",
    summary: "Registrar incidente",
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: RegisterIncidentRequestDTO::class)
    ),
    tags: ["Incident"]
)]
#[OA\Response(
    response: 200,
    description: "Incidente registrado correctamente",
    content: new OA\JsonContent(ref: RegisterIncidentResponseDTO::class)
)]
#[OA\Response(
    response: 400,
    description: "Errores de validación o lógica",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "Petición inválida"),
            new OA\Property(property: "error", properties: [
                new OA\Property(property: "message", type: "string", example: "El usuario no está relacionado con el viaje.")
            ], type: "object")
        ],
        type: "object"
    )
)]
class RegisterIncidentController extends AbstractController
{
    private RegisterIncidentUseCase $registerIncidentUseCase;

    public function __construct(RegisterIncidentUseCase $registerIncidentUseCase)
    {
        $this->registerIncidentUseCase = $registerIncidentUseCase;
    }

    public function register(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new RegisterIncidentValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }
            $dto = new RegisterIncidentRequestDTO(
                userId: (int)$data['userId'],
                travelId: (int)$data['travelId'],
                typeName: $data['typeName'],
                comment: $data['comment'] ?? null
            );
            $result = $this->registerIncidentUseCase->execute($dto);
            $responseDto = new RegisterIncidentResponseDTO(
                incidentId: $result['incidentId'],
                message: $result['message']
            );
            return $this->ok($responseDto);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
