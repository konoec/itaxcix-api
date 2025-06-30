<?php

namespace itaxcix\Core\Handler\Category;

use itaxcix\Core\UseCases\Category\CategoryCreateUseCase;
use itaxcix\Shared\DTO\useCases\Category\CategoryRequestDTO;
use itaxcix\Shared\DTO\useCases\Category\CategoryResponseDTO;

class CategoryCreateUseCaseHandler
{
    private CategoryCreateUseCase $useCase;

    public function __construct(CategoryCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(CategoryRequestDTO $request): CategoryResponseDTO
    {
        return $this->useCase->execute($request);
    }
}
