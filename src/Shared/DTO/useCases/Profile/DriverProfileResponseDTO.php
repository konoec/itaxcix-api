<?php

namespace itaxcix\Shared\DTO\useCases\Profile;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta del perfil de conductor")]
readonly class DriverProfileResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Nombre", example: "Luis")]
        public string $firstName,
        #[OA\Property(description: "Apellido", example: "García")]
        public string $lastName,
        #[OA\Property(description: "Tipo de Documento", example: "DNI")]
        public string $documentType,
        #[OA\Property(description: "Documento", example: "11223344")]
        public string $document,
        #[OA\Property(description: "Correo Electrónico", example: "conductor@correo.com")]
        public string $email,
        #[OA\Property(description: "Celular", example: "923456789")]
        public string $phone,
        #[OA\Property(description: "Calificación Promedio", example: 4.7)]
        public float $averageRating,
        #[OA\Property(description: "Número de calificaciones", example: 40)]
        public int $ratingsCount,
        #[OA\Property(description: "Placa del carro", example: "ABC-123")]
        public string $carPlate,
        #[OA\Property(description: "Estado de TUC", example: "Vigente")]
        public string $tucStatus,
        #[OA\Property(description: "Fecha de vencimiento de TUC", example: "2025-12-31")]
        public string $tucExpirationDate,
    ){}
}

