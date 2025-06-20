<?php

namespace itaxcix\Shared\DTO\useCases\Profile;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos de respuesta del perfil de administrador")]
readonly class AdminProfileResponseDTO
{
    public function __construct(
        #[OA\Property(description: "Nombre", example: "Juan")]
        public string $firstName,
        #[OA\Property(description: "Apellido", example: "Pérez")]
        public string $lastName,
        #[OA\Property(description: "Tipo de Documento", example: "DNI")]
        public string $documentType,
        #[OA\Property(description: "Documento", example: "12345678")]
        public string $document,
        #[OA\Property(description: "Área", example: "Recursos Humanos")]
        public string $area,
        #[OA\Property(description: "Cargo", example: "Administrador")]
        public string $position,
        #[OA\Property(description: "Correo Electrónico", example: "admin@correo.com")]
        public string $email,
        #[OA\Property(description: "Celular", example: "987654321")]
        public string $phone,
    ){}
}

