<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Driver;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Driver\DriverTucStatusUseCase;
use itaxcix\Core\UseCases\Driver\UpdateDriverTucUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Driver\UpdateTucResponseDto;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class DriverTucStatusController extends AbstractController
{
    private DriverTucStatusUseCase $driverTucStatusUseCase;
    private UpdateDriverTucUseCase $updateDriverTucUseCase;

    public function __construct(
        DriverTucStatusUseCase $driverTucStatusUseCase,
        UpdateDriverTucUseCase $updateDriverTucUseCase
    ) {
        $this->driverTucStatusUseCase = $driverTucStatusUseCase;
        $this->updateDriverTucUseCase = $updateDriverTucUseCase;
    }
    #[OA\Get(
        path: "/drivers/{id}/has-active-tuc",
        operationId: "checkDriverHasActiveTuc",
        description: "Verifica si el conductor tiene una TUC activa.",
        summary: "Verificar TUC activa del conductor",
        security: [["bearerAuth" => []]],
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

    // GET /drivers/{driverId}/update-tuc - Actualizar TUCs del conductor
    #[OA\Get(
        path: "/drivers/{driverId}/update-tuc",
        operationId: "updateDriverTuc",
        description: "Verifica y actualiza las TUCs de todos los vehículos del conductor consultando la API municipal. Solo registra nuevas TUCs cuando hay fechas de vigencia más recientes.",
        summary: "Actualizar TUCs del conductor",
        security: [["bearerAuth" => []]],
        tags: ["Driver", "TUC"]
    )]
    #[OA\Parameter(
        name: "driverId",
        description: "ID del conductor",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Resultado de la actualización de TUCs",
        content: new OA\JsonContent(ref: UpdateTucResponseDto::class)
    )]
    #[OA\Response(
        response: 400,
        description: "Petición inválida",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "ID de conductor inválido")
            ],
            type: "object"
        )
    )]
    public function updateDriverTuc(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $driverId = (int) $request->getAttribute('driverId');

            if ($driverId <= 0) {
                return $this->error('ID de conductor inválido', 400);
            }

            $result = $this->updateDriverTucUseCase->execute($driverId);
            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }
}