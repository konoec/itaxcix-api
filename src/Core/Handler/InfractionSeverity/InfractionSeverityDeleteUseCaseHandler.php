<?php

namespace itaxcix\Core\Handler\InfractionSeverity;

use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityDeleteUseCase;

class InfractionSeverityDeleteUseCaseHandler
{
    private InfractionSeverityDeleteUseCase $useCase;

    public function __construct(InfractionSeverityDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

