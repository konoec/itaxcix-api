<?php

namespace itaxcix\Shared\DTO\client;

readonly class VehicleResponseDTO
{
    /**
     * @param bool $found Indica si se encontró al menos un vehículo
     * @param int $count Número de vehículos encontrados
     * @param array<VehicleDTO> $vehicles Lista de vehículos encontrados
     */
    public function __construct(
        public bool  $found,
        public int   $count,
        public array $vehicles = []
    ) {
    }

    public static function notFound(): self
    {
        return new self(found: false, count: 0);
    }

    /**
     * @param array<VehicleDTO> $vehicles
     */
    public static function found(array $vehicles): self
    {
        return new self(
            found: true,
            count: count($vehicles),
            vehicles: $vehicles
        );
    }
}