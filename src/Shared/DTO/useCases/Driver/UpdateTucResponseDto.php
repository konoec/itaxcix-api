<?php

namespace itaxcix\Shared\DTO\useCases\Driver;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta de actualización de TUCs del conductor")]
readonly class UpdateTucResponseDto
{
    public function __construct(
        #[OA\Property(description: "Total de vehículos verificados", example: 1)]
        public int $totalVehiclesChecked,
        #[OA\Property(description: "Número de TUCs actualizadas", example: 2)]
        public int $tucsUpdated,
        #[OA\Property(description: "Número de TUCs que no requieren actualización", example: 1)]
        public int $tucsUpToDate,
        #[OA\Property(type: "array", items: new OA\Items(ref: TucUpdateDto::class))]
        public array $updates,
        #[OA\Property(description: "Mensaje descriptivo del proceso", example: "Se actualizaron 2 TUCs correctamente")]
        public string $message,
        #[OA\Property(description: "Información del vehículo del conductor", type: "object", nullable: true)]
        public ?VehicleInfoDto $vehicleInfo = null,
        #[OA\Property(description: "Todas las TUCs del vehículo", type: "array", items: new OA\Items(ref: TucInfoDto::class))]
        public array $allTucs = [],
        #[OA\Property(description: "TUC con fecha de vencimiento más lejana", ref: TucInfoDto::class, nullable: true)]
        public ?TucInfoDto $furthestExpiringTuc = null
    ) {}
}
