<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Driver;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Driver\DriverTucStatusUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DriverTucStatusController extends AbstractController
{
    private DriverTucStatusUseCase $driverTucStatusUseCase;
    public function __construct(DriverTucStatusUseCase $driverTucStatusUseCase)
    {
        $this->driverTucStatusUseCase = $driverTucStatusUseCase;
    }
    #[OA\Get(
        path: "/drivers/{id}/has-active-tuc",
        operationId: "checkDriverHasActiveTuc",
        description: "Verifica si el conductor tiene una TUC activa.",
        summary: "Verificar TUC activa del conductor",
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
                description: "Consulta realizada correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "OK"),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "driverId", type: "integer", example: 123),
                            new OA\Property(property: "hasActiveTuc", type: "boolean", example: true)
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
                description: "Conductor no encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Conductor no encontrado"),
                        new OA\Property(property: "error", properties: [
                            new OA\Property(property: "message", type: "string", example: "Conductor no encontrado")
                        ], type: "object")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function hasActiveTuc(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $driverId = (int) $request->getAttribute('id');

            if ($driverId <= 0) {
                return $this->error('ID de conductor inválido', 400);
            }

            // Aquí deberías llamar a tu caso de uso o servicio para verificar la TUC activa
            $hasActiveTuc = $this->driverTucStatusUseCase->execute($driverId);

            if ($hasActiveTuc === null) {
                return $this->error('Conductor no encontrado', 404);
            }

            return $this->ok($hasActiveTuc);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}