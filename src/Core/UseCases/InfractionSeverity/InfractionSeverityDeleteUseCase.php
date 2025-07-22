<?php

namespace itaxcix\Core\UseCases\InfractionSeverity;

use itaxcix\Core\Interfaces\infraction\InfractionRepositoryInterface;
use itaxcix\Core\Interfaces\infraction\InfractionSeverityRepositoryInterface;

class InfractionSeverityDeleteUseCase
{
    private InfractionSeverityRepositoryInterface $repository;
    private InfractionRepositoryInterface $infractionRepository;

    public function __construct(InfractionSeverityRepositoryInterface $repository, InfractionRepositoryInterface $infractionRepository)
    {
        $this->repository = $repository;
        $this->infractionRepository = $infractionRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the infraction severity is associated with any infractions
        $infractions = $this->infractionRepository->findActivesBySeverityId($id);
        if (!empty($infractions)) {
            return false; // Cannot delete if there are associated infractions
        }

        return $this->repository->delete($id);
    }
}

