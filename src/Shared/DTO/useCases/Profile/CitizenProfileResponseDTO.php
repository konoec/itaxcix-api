<?php

namespace itaxcix\Shared\DTO\useCases\Profile;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta del perfil de ciudadano")]
readonly class CitizenProfileResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Nombre", example: "Carlos")]
        public string $firstName,
        #[OA\Property(description: "Apellido", example: "Ramírez")]
        public string $lastName,
        #[OA\Property(description: "Tipo de Documento", example: "DNI")]
        public string $documentType,
        #[OA\Property(description: "Documento", example: "87654321")]
        public string $document,
        #[OA\Property(description: "Correo Electrónico", example: "ciudadano@correo.com")]
        public string $email,
        #[OA\Property(description: "Celular", example: "912345678")]
        public string $phone,
        #[OA\Property(description: "Calificación Promedio", example: 4.8)]
        public float $averageRating,
        #[OA\Property(description: "Número de calificaciones", example: 25)]
        public int $ratingsCount,
    ){}
}

