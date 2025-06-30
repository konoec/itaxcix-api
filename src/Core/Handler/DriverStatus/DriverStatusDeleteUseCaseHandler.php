<?php

namespace itaxcix\Core\Handler\DriverStatus;

use itaxcix\Core\UseCases\DriverStatus\DriverStatusDeleteUseCase;

class DriverStatusDeleteUseCaseHandler
{
    private DriverStatusDeleteUseCase $useCase;

    public function __construct(DriverStatusDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): array
    {
        return $this->useCase->execute($id);
    }
}
