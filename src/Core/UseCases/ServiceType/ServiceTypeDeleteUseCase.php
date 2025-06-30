<?php

namespace itaxcix\Core\UseCases\ServiceType;

use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;

class ServiceTypeDeleteUseCase
{
    private ServiceTypeRepositoryInterface $repository;

    public function __construct(ServiceTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

