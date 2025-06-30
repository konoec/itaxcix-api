<?php

namespace itaxcix\Core\UseCases\IncidentType;

use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;

class IncidentTypeDeleteUseCase
{
    private IncidentTypeRepositoryInterface $repository;

    public function __construct(IncidentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

