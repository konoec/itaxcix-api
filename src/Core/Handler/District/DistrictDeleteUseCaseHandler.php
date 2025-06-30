<?php

namespace itaxcix\Core\Handler\District;

use itaxcix\Core\UseCases\District\DistrictDeleteUseCase;

class DistrictDeleteUseCaseHandler
{
    private DistrictDeleteUseCase $useCase;

    public function __construct(DistrictDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
