<?php

namespace itaxcix\Core\Handler\VehicleClass;

use itaxcix\Core\UseCases\VehicleClass\VehicleClassDeleteUseCase;

class VehicleClassDeleteUseCaseHandler
{
    private VehicleClassDeleteUseCase $useCase;

    public function __construct(VehicleClassDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
