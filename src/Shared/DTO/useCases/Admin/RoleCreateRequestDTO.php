<?php

namespace itaxcix\Shared\DTO\useCases\Admin;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Datos para crear un rol")]
readonly class RoleCreateRequestDTO
{
    public function __construct(
        #[OA\Property(description: "Nombre del rol", example: "Administrador")]
        public string $name,
        #[OA\Property(description: "Si el rol está activo", example: true)]
        public bool $active = true,
        #[OA\Property(description: "Si el rol es para web", example: false)]
        public bool $web = false
    ) {}
}
