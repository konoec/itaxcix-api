<?php

namespace itaxcix\Core\Handler\InfractionSeverity;

use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityUpdateUseCase;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityResponseDTO;

class InfractionSeverityUpdateUseCaseHandler
{
    private InfractionSeverityUpdateUseCase $useCase;

    public function __construct(InfractionSeverityUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, InfractionSeverityRequestDTO $request): InfractionSeverityResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

