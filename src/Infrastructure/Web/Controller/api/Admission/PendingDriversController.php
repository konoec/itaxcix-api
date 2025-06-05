<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admission;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admission\GetDriverDetailsUseCase;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Admission\PendingDriverDetailsResponseDTO;
use itaxcix\Shared\DTO\useCases\Admission\PendingDriverResponseDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use OpenApi\Attributes as OA;

class PendingDriversController extends AbstractController
{
    private GetDriverDetailsUseCase $getDriverDetailsUseCase;
    private GetPendingDriversUseCase $getPendingDriversUseCase;
    public function __construct(GetDriverDetailsUseCase $getDriverDetailsUseCase, GetPendingDriversUseCase $getPendingDriversUseCase)
    {
        $this->getDriverDetailsUseCase = $getDriverDetailsUseCase;
        $this->getPendingDriversUseCase = $getPendingDriversUseCase;
    }

    #[OA\Get(
        path: "/drivers/pending",
        operationId: "getAllPendingDrivers",
        description: "Retorna un arreglo con la información básica de los conductores en estado pendiente.",
        summary: "Obtiene todos los conductores pendientes",
        security: [["bearerAuth"=>[]]],
        tags: ["Admission"]
    )]
    #[OA\Response(
        response: 200,
        description: "Listado de conductores pendientes",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: PendingDriverResponseDTO::class)
        )
    )]
    #[OA\Response(
        response: 404,
        description: "No se encontraron conductores pendientes",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "No se encontraron conductores pendientes")
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
                        new OA\Property(property: "message", type: "string", example: "No se pudo obtener los conductores pendientes.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function getAllPendingDrivers(ServerRequestInterface $request): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $page    = max(1, (int) ($query['page'] ?? 1));
        $perPage = max(1, (int) ($query['perPage'] ?? 10));

        try {
            $paginated = $this->getPendingDriversUseCase->execute($page, $perPage);
            return $this->ok($paginated);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: "/drivers/pending/{id}",
        operationId: "getDriverDetails",
        description: "Retorna la información detallada de un conductor en estado pendiente según su ID.",
        summary: "Obtiene los detalles de un conductor pendiente",
        security: [["bearerAuth"=>[]]],
        tags: ["Admission"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID del conductor pendiente",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 123)
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Detalles del conductor pendiente",
        content: new OA\JsonContent(ref: PendingDriverDetailsResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: "ID de conductor inválido o error de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "ID de conductor inválido")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Conductor no encontrado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Conductor no encontrado")
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
                        new OA\Property(property: "message", type: "string", example: "No se pudo obtener los detalles del conductor pendiente.")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function getDriverDetails(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $driverId = (int)$request->getAttribute('id');

            if ($driverId <= 0) {
                return $this->error('ID de conductor inválido', 400);
            }

            $driverDetailsResponse = $this->getDriverDetailsUseCase->execute($driverId);

            if ($driverDetailsResponse === null) {
                return $this->error('Conductor no encontrado', 404);
            }

            return $this->ok($driverDetailsResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}