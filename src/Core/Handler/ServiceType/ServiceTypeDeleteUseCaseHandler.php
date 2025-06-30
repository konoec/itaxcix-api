<?php

namespace itaxcix\Core\Handler\ServiceType;

use itaxcix\Core\UseCases\ServiceType\ServiceTypeDeleteUseCase;

class ServiceTypeDeleteUseCaseHandler
{
    private ServiceTypeDeleteUseCase $useCase;

    public function __construct(ServiceTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

