<?php

namespace itaxcix\Core\Handler\IncidentType;

use itaxcix\Core\UseCases\IncidentType\IncidentTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeResponseDTO;

class IncidentTypeCreateUseCaseHandler
{
    private IncidentTypeCreateUseCase $useCase;

    public function __construct(IncidentTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(IncidentTypeRequestDTO $request): IncidentTypeResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

