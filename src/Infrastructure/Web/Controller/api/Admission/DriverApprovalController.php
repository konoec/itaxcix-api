<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admission;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admission\ApproveDriverAdmissionUseCase;
use itaxcix\Core\UseCases\Admission\RejectDriverAdmissionUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Admission\ApproveDriverRequestDto;
use itaxcix\Shared\DTO\useCases\Admission\RejectDriverRequestDto;
use itaxcix\Shared\Validators\useCases\Admission\ApproveDriverValidator;
use itaxcix\Shared\Validators\useCases\Admission\RejectDriverValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use OpenApi\Attributes as OA;

class DriverApprovalController extends AbstractController
{
    private ApproveDriverAdmissionUseCase $approveDriverUseCase;
    private RejectDriverAdmissionUseCase $rejectDriverUseCase;

    public function __construct(ApproveDriverAdmissionUseCase $approveDriverUseCase, RejectDriverAdmissionUseCase $rejectDriverUseCase)
    {
        $this->approveDriverUseCase = $approveDriverUseCase;
        $this->rejectDriverUseCase = $rejectDriverUseCase;
    }

    #[OA\Post(
        path: "/drivers/approve",
        operationId: "approveDriver",
        description: "Recibe el ID de un conductor en estado pendiente y lo aprueba.",
        summary: "Aprueba un conductor pendiente",
        security: [["bearerAuth"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: ApproveDriverRequestDto::class)
        ),
        tags: ["Admission"]
    )]
    #[OA\Response(
        response: 200,
        description: "Conductor aprobado con éxito",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "El conductor ha sido aprobado."),
                new OA\Property(property: "data", type: "object", example: [])
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error de validación o lógica de negocio",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "ID de conductor inválido"),
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El conductor no existe o no está pendiente.")
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
                        new OA\Property(property: "message", type: "string", example: "No se pudo procesar la aprobación.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function approveDriver(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validator = new ApproveDriverValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO de entrada
            $approveDriverDto = new ApproveDriverRequestDto(
                driverId: $data['driverId']
            );

            // Usar el UseCase
            $authResponse = $this->approveDriverUseCase->execute($approveDriverDto);

            if (!$authResponse) {
                return $this->error('No se pudo aprobar al conductor', 400);
            }

            return $this->ok($authResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/drivers/reject",
        operationId: "rejectDriver",
        description: "Recibe el ID de un conductor en estado pendiente y lo rechaza.",
        summary: "Rechaza un conductor pendiente",
        security: [["bearerAuth"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: RejectDriverRequestDto::class)
        ),
        tags: ["Admission"]
    )]
    #[OA\Response(
        response: 200,
        description: "Conductor rechazado con éxito",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "El conductor ha sido rechazado."),
                new OA\Property(property: "data", type: "object", example: [])
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error de validación o lógica de negocio",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "ID de conductor inválido"),
                new OA\Property(
                    property: "error",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El conductor no existe o no está pendiente.")
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
                        new OA\Property(property: "message", type: "string", example: "No se pudo procesar el rechazo.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function rejectDriver(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validator = new RejectDriverValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO de entrada
            $rejectDriverDto = new RejectDriverRequestDto(
                driverId: $data['driverId']
            );

            // Usar el UseCase
            $authResponse = $this->rejectDriverUseCase->execute($rejectDriverDto);

            if (!$authResponse) {
                return $this->error('No se pudo rechazar al conductor', 400);
            }

            return $this->ok($authResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}