<?php

namespace itaxcix\Core\Handler\Brand;

use itaxcix\Core\UseCases\Brand\BrandDeleteUseCase;

class BrandDeleteUseCaseHandler
{
    private BrandDeleteUseCase $useCase;

    public function __construct(BrandDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
