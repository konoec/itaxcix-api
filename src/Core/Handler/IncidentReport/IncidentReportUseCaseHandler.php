<?php

namespace itaxcix\Core\Handler\IncidentReport;

use itaxcix\Core\UseCases\IncidentReport\IncidentReportUseCase;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportPaginationResponseDTO;

class IncidentReportUseCaseHandler
{
    private IncidentReportUseCase $useCase;

    public function __construct(IncidentReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(IncidentReportRequestDTO $dto): IncidentReportPaginationResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

