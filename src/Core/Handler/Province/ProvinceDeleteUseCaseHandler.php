<?php

namespace itaxcix\Core\Handler\Province;

use itaxcix\Core\UseCases\Province\ProvinceDeleteUseCase;

class ProvinceDeleteUseCaseHandler
{
    private ProvinceDeleteUseCase $useCase;

    public function __construct(ProvinceDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
