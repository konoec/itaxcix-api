<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Driver;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Driver\ToggleDriverStatusUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DriverStatusController extends AbstractController
{
    private ToggleDriverStatusUseCase $toggleDriverStatusUseCase;
    public function __construct(ToggleDriverStatusUseCase $toggleDriverStatusUseCase)
    {
        $this->toggleDriverStatusUseCase = $toggleDriverStatusUseCase;
    }
    #[OA\Patch(
        path: "/drivers/{id}/toggle-active",
        operationId: "toggleDriverActiveStatus",
        description: "Activa o desactiva el estado de disponibilidad de un conductor.",
        summary: "Cambiar estado activo del conductor",
        tags: ["Driver"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID del conductor",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 123)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estado actualizado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "OK"),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "driverId", type: "integer", example: 123),
                            new OA\Property(property: "available", type: "boolean", example: true)
                        ], type: "object")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "ID de conductor inválido o error de validación",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "ID de conductor inválido"),
                        new OA\Property(property: "error", properties: [
                            new OA\Property(property: "message", type: "string", example: "ID de conductor inválido")
                        ], type: "object")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Conductor no encontrado o error al cambiar el estado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Conductor no encontrado o error al cambiar el estado"),
                        new OA\Property(property: "error", properties: [
                            new OA\Property(property: "message", type: "string", example: "Conductor no encontrado")
                        ], type: "object")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function toggleActiveStatus(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $driverId = (int) $request->getAttribute('id');

            if ($driverId <= 0) {
                return $this->error('ID de conductor inválido', 400);
            }

            $driverStatus = $this->toggleDriverStatusUseCase->execute($driverId);

            if ($driverStatus === null) {
                return $this->error('Conductor no encontrado o error al cambiar el estado', 404);
            }

            return $this->ok($driverStatus);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}