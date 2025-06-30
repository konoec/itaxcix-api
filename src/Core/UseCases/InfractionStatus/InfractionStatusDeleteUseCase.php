<?php

namespace itaxcix\Core\UseCases\InfractionStatus;

use itaxcix\Core\Interfaces\infraction\InfractionStatusRepositoryInterface;

class InfractionStatusDeleteUseCase
{
    private InfractionStatusRepositoryInterface $repository;

    public function __construct(InfractionStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

