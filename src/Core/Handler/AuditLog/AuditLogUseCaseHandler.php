<?php

namespace itaxcix\Core\Handler\AuditLog;

use itaxcix\Core\UseCases\AuditLog\AuditLogUseCase;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogRequestDTO;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogPaginationResponseDTO;

class AuditLogUseCaseHandler
{
    private AuditLogUseCase $useCase;

    public function __construct(AuditLogUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(AuditLogRequestDTO $dto): AuditLogPaginationResponseDTO
    {
        return $this->useCase->handle($dto);
    }

    public function getDetails(int $id)
    {
        return $this->useCase->getDetails($id);
    }

    public function export(AuditLogRequestDTO $dto): string
    {
        return $this->useCase->export($dto);
    }
}

