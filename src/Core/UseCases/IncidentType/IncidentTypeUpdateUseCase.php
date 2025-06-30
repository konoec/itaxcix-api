<?php

namespace itaxcix\Core\UseCases\IncidentType;

use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeResponseDTO;

class IncidentTypeUpdateUseCase
{
    private IncidentTypeRepositoryInterface $repository;

    public function __construct(IncidentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, IncidentTypeRequestDTO $request): IncidentTypeResponseDTO
    {
        $incidentType = new IncidentTypeModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updatedIncidentType = $this->repository->saveIncidentType($incidentType);

        return new IncidentTypeResponseDTO(
            id: $updatedIncidentType->getId(),
            name: $updatedIncidentType->getName(),
            active: $updatedIncidentType->isActive()
        );
    }
}

