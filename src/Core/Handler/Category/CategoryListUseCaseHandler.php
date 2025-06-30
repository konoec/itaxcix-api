<?php

namespace itaxcix\Core\Handler\Category;

use itaxcix\Core\UseCases\Category\CategoryListUseCase;
use itaxcix\Shared\DTO\useCases\Category\CategoryPaginationRequestDTO;

class CategoryListUseCaseHandler
{
    private CategoryListUseCase $useCase;

    public function __construct(CategoryListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(CategoryPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
