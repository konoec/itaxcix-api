<?php

namespace itaxcix\Core\UseCases\VehicleClass;

use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;

class VehicleClassDeleteUseCase
{
    private VehicleClassRepositoryInterface $repository;

    public function __construct(VehicleClassRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
