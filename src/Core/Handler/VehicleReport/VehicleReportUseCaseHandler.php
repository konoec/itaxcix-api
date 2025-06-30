<?php

namespace itaxcix\Core\Handler\VehicleReport;

use itaxcix\Core\UseCases\VehicleReport\VehicleReportUseCase;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportPaginationResponseDTO;

class VehicleReportUseCaseHandler
{
    private VehicleReportUseCase $useCase;

    public function __construct(VehicleReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(VehicleReportRequestDTO $dto): VehicleReportPaginationResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

