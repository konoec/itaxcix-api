<?php

namespace itaxcix\Core\Handler\Category;

use itaxcix\Core\UseCases\Category\CategoryDeleteUseCase;

class CategoryDeleteUseCaseHandler
{
    private CategoryDeleteUseCase $useCase;

    public function __construct(CategoryDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
