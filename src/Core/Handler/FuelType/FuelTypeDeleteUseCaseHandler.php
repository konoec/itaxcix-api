<?php

namespace itaxcix\Core\Handler\FuelType;

use itaxcix\Core\UseCases\FuelType\FuelTypeDeleteUseCase;

class FuelTypeDeleteUseCaseHandler
{
    private FuelTypeDeleteUseCase $useCase;

    public function __construct(FuelTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
