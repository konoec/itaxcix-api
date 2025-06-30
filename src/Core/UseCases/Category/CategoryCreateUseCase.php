<?php

namespace itaxcix\Core\UseCases\Category;

use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;
use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Category\CategoryRequestDTO;
use itaxcix\Shared\DTO\useCases\Category\CategoryResponseDTO;

class CategoryCreateUseCase
{
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;

    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository)
    {
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
    }

    public function execute(CategoryRequestDTO $request): CategoryResponseDTO
    {
        $categoryModel = new VehicleCategoryModel(
            id: null,
            name: trim($request->name),
            active: $request->active
        );

        $createdCategory = $this->vehicleCategoryRepository->create($categoryModel);

        return CategoryResponseDTO::fromModel($createdCategory);
    }
}
