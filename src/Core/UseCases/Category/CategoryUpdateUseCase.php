<?php

namespace itaxcix\Core\UseCases\Category;

use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;
use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Category\CategoryRequestDTO;
use itaxcix\Shared\DTO\useCases\Category\CategoryResponseDTO;

class CategoryUpdateUseCase
{
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;

    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository)
    {
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
    }

    public function execute(int $id, CategoryRequestDTO $request): CategoryResponseDTO
    {
        $categoryModel = new VehicleCategoryModel(
            id: $id,
            name: trim($request->name),
            active: $request->active
        );

        $updatedCategory = $this->vehicleCategoryRepository->update($categoryModel);

        return CategoryResponseDTO::fromModel($updatedCategory);
    }
}
