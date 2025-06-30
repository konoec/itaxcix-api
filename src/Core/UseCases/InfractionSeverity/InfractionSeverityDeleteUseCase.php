<?php

namespace itaxcix\Core\UseCases\InfractionSeverity;

use itaxcix\Core\Interfaces\infraction\InfractionSeverityRepositoryInterface;

class InfractionSeverityDeleteUseCase
{
    private InfractionSeverityRepositoryInterface $repository;

    public function __construct(InfractionSeverityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

