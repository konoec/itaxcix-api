<?php

namespace itaxcix\Shared\DTO\useCases\HelpCenter;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta con un elemento del centro de ayuda")]
readonly class HelpCenterResponseDTO {
    public function __construct(
        #[OA\Property(description: "ID del elemento", example: 1)]
        public int $id,
        #[OA\Property(description: "Título de la sección", example: "Viajes")]
        public string $title,
        #[OA\Property(description: "Subtítulo de la pregunta", example: "¿Cómo solicitar un viaje?")]
        public string $subtitle,
        #[OA\Property(description: "Respuesta detallada", example: "Para solicitar un viaje, abre la aplicación y selecciona tu destino.")]
        public string $answer,
        #[OA\Property(description: "Si el elemento está activo", example: true)]
        public bool $active,
    ) {}
}
