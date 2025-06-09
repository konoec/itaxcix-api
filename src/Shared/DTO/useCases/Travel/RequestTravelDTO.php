<?php

namespace itaxcix\Shared\DTO\useCases\Travel;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "DTO de solicitud de viaje")]
readonly class RequestTravelDTO
{
    public function __construct(
        #[OA\Property(description: "id", example: 12345)]
        public int $citizenId,
        #[OA\Property(description: "id del conductor", example: 67890)]
        public int $driverId,
        #[OA\Property(description: "Latitud de origen", example: "12.345678")]
        public float $originLatitude,
        #[OA\Property(description: "Longitud de origen", example: "-12.345678")]
        public float $originLongitude,
        #[OA\Property(description: "Distrito de origen", example: "Distrito Central")]
        public string $originDistrict,
        #[OA\Property(description: "Dirección de origen", example: "Calle Falsa 123")]
        public string $originAddress,
        #[OA\Property(description: "Latitud de destino", example: "12.345678")]
        public float $destinationLatitude,
        #[OA\Property(description: "Longitud de destino", example: "-12.345678")]
        public float $destinationLongitude,
        #[OA\Property(description: "Distrito de destino", example: "Distrito Sur")]
        public string $destinationDistrict,
        #[OA\Property(description: "Dirección de destino", example: "Avenida Siempre Viva 456")]
        public string $destinationAddress,
    ) {}
}