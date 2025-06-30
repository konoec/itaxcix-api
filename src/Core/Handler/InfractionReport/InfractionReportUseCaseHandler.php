<?php

namespace itaxcix\Core\Handler\InfractionReport;

use itaxcix\Core\UseCases\InfractionReport\InfractionReportUseCase;
use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportPaginationResponseDTO;

class InfractionReportUseCaseHandler
{
    private InfractionReportUseCase $useCase;

    public function __construct(InfractionReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(InfractionReportRequestDTO $dto): InfractionReportPaginationResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

