<?php

namespace itaxcix\Core\Handler\RatingReport;

use itaxcix\Core\UseCases\RatingReport\RatingReportUseCase;
use itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO;
use itaxcix\Shared\DTO\useCases\RatingReport\RatingReportPaginationResponseDTO;

class RatingReportUseCaseHandler
{
    private RatingReportUseCase $useCase;

    public function __construct(RatingReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(RatingReportRequestDTO $dto): RatingReportPaginationResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

