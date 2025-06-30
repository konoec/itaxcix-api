<?php

namespace itaxcix\Core\Handler\InfractionSeverity;

use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityListUseCase;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityPaginationRequestDTO;

class InfractionSeverityListUseCaseHandler
{
    private InfractionSeverityListUseCase $useCase;

    public function __construct(InfractionSeverityListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(InfractionSeverityPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

