<?php

namespace itaxcix\Shared\DTO\useCases\Travel;

use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Respuesta paginada del historial de viajes")]
readonly class TravelHistoryResponseDto
{
    public function __construct(
        #[OA\Property(type: "array", items: new OA\Items(ref: TravelHistoryItemDto::class))]
        public array $items,
        #[OA\Property(ref: PaginationMetaDTO::class)]
        public PaginationMetaDTO $meta
    ) {}
}
