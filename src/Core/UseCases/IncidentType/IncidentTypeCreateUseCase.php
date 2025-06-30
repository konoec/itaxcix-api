<?php

namespace itaxcix\Core\UseCases\IncidentType;

use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeResponseDTO;

class IncidentTypeCreateUseCase
{
    private IncidentTypeRepositoryInterface $repository;

    public function __construct(IncidentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(IncidentTypeRequestDTO $request): IncidentTypeResponseDTO
    {
        $incidentType = new IncidentTypeModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $savedIncidentType = $this->repository->saveIncidentType($incidentType);

        return new IncidentTypeResponseDTO(
            id: $savedIncidentType->getId(),
            name: $savedIncidentType->getName(),
            active: $savedIncidentType->isActive()
        );
    }
}

