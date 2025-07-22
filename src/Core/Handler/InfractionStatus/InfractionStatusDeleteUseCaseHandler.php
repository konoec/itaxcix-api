<?php

namespace itaxcix\Core\Handler\InfractionStatus;

use itaxcix\Core\Interfaces\infraction\InfractionRepositoryInterface;
use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusDeleteUseCase;

class InfractionStatusDeleteUseCaseHandler
{
    private InfractionStatusDeleteUseCase $useCase;
    private InfractionRepositoryInterface $infractionRepository;

    public function __construct(InfractionStatusDeleteUseCase $useCase, InfractionRepositoryInterface $infractionRepository)
    {
        $this->useCase = $useCase;
        $this->infractionRepository = $infractionRepository;
    }

    public function handle(int $id): bool
    {
        // Check if the infraction status is associated with any infractions
        $infractions = $this->infractionRepository->findActivesByStatusId($id);
        if (!empty($infractions)) {
            return false; // Cannot delete if there are associated infractions
        }

        return $this->useCase->execute($id);
    }
}

