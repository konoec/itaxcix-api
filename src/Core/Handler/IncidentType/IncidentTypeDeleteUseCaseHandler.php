<?php

namespace itaxcix\Core\Handler\IncidentType;

use itaxcix\Core\UseCases\IncidentType\IncidentTypeDeleteUseCase;

class IncidentTypeDeleteUseCaseHandler
{
    private IncidentTypeDeleteUseCase $useCase;

    public function __construct(IncidentTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

