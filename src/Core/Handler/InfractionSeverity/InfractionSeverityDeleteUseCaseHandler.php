<?php

namespace itaxcix\Core\Handler\InfractionSeverity;

use itaxcix\Core\Interfaces\infraction\InfractionRepositoryInterface;
use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityDeleteUseCase;

class InfractionSeverityDeleteUseCaseHandler
{
    private InfractionSeverityDeleteUseCase $useCase;
    private InfractionRepositoryInterface $infractionRepository;

    public function __construct(InfractionSeverityDeleteUseCase $useCase, InfractionRepositoryInterface $infractionRepository)
    {
        $this->useCase = $useCase;
        $this->infractionRepository = $infractionRepository;
    }

    public function handle(int $id): bool
    {
        // Check if the infraction severity is associated with any infractions
        $infractions = $this->infractionRepository->findActivesBySeverityId($id);
        if (!empty($infractions)) {
            return false; // Cannot delete if there are associated infractions
        }

        return $this->useCase->execute($id);
    }
}

