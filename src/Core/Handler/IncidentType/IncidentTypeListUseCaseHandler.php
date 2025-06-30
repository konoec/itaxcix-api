<?php

namespace itaxcix\Core\Handler\IncidentType;

use itaxcix\Core\UseCases\IncidentType\IncidentTypeListUseCase;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypePaginationRequestDTO;

class IncidentTypeListUseCaseHandler
{
    private IncidentTypeListUseCase $useCase;

    public function __construct(IncidentTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(IncidentTypePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

