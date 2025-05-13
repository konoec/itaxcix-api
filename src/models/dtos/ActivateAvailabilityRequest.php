<?php
namespace itaxcix\models\dtos;

use InvalidArgumentException;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ActivateAvailabilityRequest", description: "Datos para desactivar la disponibilidad de un ciudadano")]
class ActivateAvailabilityRequest {

    #[OA\Property(property: "userId", type: "integer", example: 1)]
    public int $userId;

    public function __construct(array $data) {
        $this->userId = $data['userId'] ?? throw new InvalidArgumentException('userId es requerido');
    }
}