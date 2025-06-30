<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use Exception;
use itaxcix\Core\UseCases\Vehicle\AssociateUserVehicleUseCase;
use itaxcix\Core\UseCases\Vehicle\DisassociateUserVehicleUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Vehicle\AssociateVehicleRequestDto;
use itaxcix\Shared\DTO\useCases\Vehicle\AssociateVehicleResponseDto;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VehicleAssociationController extends AbstractController
{
    private DisassociateUserVehicleUseCase $disassociateUseCase;
    private AssociateUserVehicleUseCase $associateUseCase;

    public function __construct(
        DisassociateUserVehicleUseCase $disassociateUseCase,
        AssociateUserVehicleUseCase $associateUseCase
    ) {
        $this->disassociateUseCase = $disassociateUseCase;
        $this->associateUseCase = $associateUseCase;
    }

    // DELETE /users/{userId}/vehicle/association - Desasociar vehículo del usuario
    #[OA\Delete(
        path: "/users/{userId}/vehicle/association",
        operationId: "disassociateUserVehicle",
        description: "Desasocia el vehículo actualmente asociado al usuario. El usuario quedará sin vehículo asociado.",
        summary: "Desasociar vehículo del usuario",
        security: [["bearerAuth" => []]],
        tags: ["Vehicle", "User"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Vehículo desasociado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Vehículo desasociado exitosamente")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error en la petición",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "El usuario no tiene ningún vehículo asociado")
            ],
            type: "object"
        )
    )]
    public function disassociateVehicle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int) $request->getAttribute('userId');

            if ($userId <= 0) {
                return $this->error('ID de usuario inválido', 400);
            }

            $result = $this->disassociateUseCase->execute($userId);

            return $this->ok([
                "success" => true,
                "message" => "Vehículo desasociado exitosamente"
            ]);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }

    // POST /users/{userId}/vehicle/association - Asociar vehículo al usuario
    #[OA\Post(
        path: "/users/{userId}/vehicle/association",
        operationId: "associateUserVehicle",
        description: "Asocia un vehículo al usuario usando su placa. Si el vehículo no existe, lo crea consultando la API municipal. También actualiza automáticamente las TUCs del vehículo.",
        summary: "Asociar vehículo al usuario por placa",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "plateValue", type: "string", example: "ABC-123", description: "Placa del vehículo a asociar")
                ],
                type: "object"
            )
        ),
        tags: ["Vehicle", "User"]
    )]
    #[OA\Parameter(
        name: "userId",
        description: "ID del usuario",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Vehículo asociado exitosamente",
        content: new OA\JsonContent(ref: AssociateVehicleResponseDto::class)
    )]
    #[OA\Response(
        response: 400,
        description: "Error en la validación o petición",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "El usuario ya tiene un vehículo asociado")
            ],
            type: "object"
        )
    )]
    public function associateVehicle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $userId = (int) $request->getAttribute('userId');
            $data = $this->getJsonBody($request);

            if ($userId <= 0) {
                return $this->error('ID de usuario inválido', 400);
            }

            if (empty($data['plateValue'])) {
                return $this->error('La placa del vehículo es requerida', 400);
            }

            $dto = new AssociateVehicleRequestDto(
                userId: $userId,
                plateValue: trim(strtoupper($data['plateValue']))
            );

            $result = $this->associateUseCase->execute($dto);

            return $this->ok($result);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), 400);
        }
    }
}
