<?php

namespace itaxcix\Core\Handler\IncidentType;

use itaxcix\Core\UseCases\IncidentType\IncidentTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeResponseDTO;

class IncidentTypeUpdateUseCaseHandler
{
    private IncidentTypeUpdateUseCase $useCase;

    public function __construct(IncidentTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, IncidentTypeRequestDTO $request): IncidentTypeResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

