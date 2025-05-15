<?php

namespace itaxcix\models\dtos;

use InvalidArgumentException;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "AttachVehicleRequest", description: "Datos para asociar un vehículo de un ciudadano")]
class AttachVehicleRequest {

    #[OA\Property(property: "userId", type: "integer", example: 1)]
    public int $userId;
    #[OA\Property(property: "vehicleId", type: "integer", example: 1)]
    public int $vehicleId;

    public function __construct(array $data) {
        $this->userId = $data['userId'] ?? throw new InvalidArgumentException('userId es requerido');
        $this->vehicleId = $data['vehicleId'] ?? throw new InvalidArgumentException('vehicleId es requerido');
    }
}