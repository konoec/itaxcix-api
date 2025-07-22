<?php

namespace itaxcix\Core\UseCases\IncidentType;

use itaxcix\Core\Interfaces\incident\IncidentRepositoryInterface;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Shared\Validators\useCases\IncidentType\IncidentTypeValidator;

class IncidentTypeDeleteUseCase
{
    private IncidentTypeRepositoryInterface $repository;
    private IncidentRepositoryInterface $incidentRepository;

    public function __construct(IncidentTypeRepositoryInterface $repository, IncidentRepositoryInterface $incidentRepository)
    {
        $this->repository = $repository;
        $this->incidentRepository = $incidentRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the incident type is associated with any incidents
        $incidents = $this->incidentRepository->findActivesByIncidentTypeId($id);
        if (!empty($incidents)) {
            return false; // Cannot delete if there are associated incidents
        }

        return $this->repository->delete($id);
    }
}

