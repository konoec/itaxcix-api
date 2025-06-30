<?php

namespace itaxcix\Core\Handler\InfractionSeverity;

use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityCreateUseCase;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityResponseDTO;

class InfractionSeverityCreateUseCaseHandler
{
    private InfractionSeverityCreateUseCase $useCase;

    public function __construct(InfractionSeverityCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(InfractionSeverityRequestDTO $request): InfractionSeverityResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

