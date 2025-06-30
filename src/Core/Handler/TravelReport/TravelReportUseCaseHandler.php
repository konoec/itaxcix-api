<?php

namespace itaxcix\Core\Handler\TravelReport;

use itaxcix\Core\UseCases\TravelReport\TravelReportUseCase;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportPaginationResponseDTO;

class TravelReportUseCaseHandler
{
    private TravelReportUseCase $useCase;

    public function __construct(TravelReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TravelReportRequestDTO $dto): TravelReportPaginationResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

