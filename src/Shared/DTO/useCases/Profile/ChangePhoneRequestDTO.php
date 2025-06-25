<?php

namespace itaxcix\Shared\DTO\useCases\Profile;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos necesarios para solicitar el cambio de número telefónico")]
readonly class ChangePhoneRequestDTO
{
    public function __construct(
        #[OA\Property(description: "ID del usuario", example: 1)]
        public int $userId,
        #[OA\Property(description: "Nuevo número telefónico", example: "593987654321")]
        public string $phone
    ) {}
}
