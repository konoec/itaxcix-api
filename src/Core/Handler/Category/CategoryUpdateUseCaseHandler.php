<?php

namespace itaxcix\Core\Handler\Category;

use itaxcix\Core\UseCases\Category\CategoryUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Category\CategoryRequestDTO;
use itaxcix\Shared\DTO\useCases\Category\CategoryResponseDTO;

class CategoryUpdateUseCaseHandler
{
    private CategoryUpdateUseCase $useCase;

    public function __construct(CategoryUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, CategoryRequestDTO $request): CategoryResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}
