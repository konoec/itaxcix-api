<?php

namespace itaxcix\Core\Handler\UserReport;

use itaxcix\Core\UseCases\UserReport\UserReportUseCase;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportRequestDTO;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportPaginationResponseDTO;

class UserReportUseCaseHandler
{
    private UserReportUseCase $useCase;

    public function __construct(UserReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(UserReportRequestDTO $dto): UserReportPaginationResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

