<?php

namespace itaxcix\Core\UseCases\Category;

use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Category\CategoryPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Category\CategoryResponseDTO;

class CategoryListUseCase
{
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;

    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository)
    {
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
    }

    public function execute(CategoryPaginationRequestDTO $request): array
    {
        $categories = $this->vehicleCategoryRepository->findWithPagination(
            $request->page,
            $request->perPage,
            $request->getFilters(),
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->vehicleCategoryRepository->getTotalCount($request->getFilters());
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $categoryDTOs = array_map(function($category) {
            return CategoryResponseDTO::fromModel($category);
        }, $categories);

        return [
            'data' => array_map(fn($dto) => $dto->toArray(), $categoryDTOs),
            'pagination' => [
                'current_page' => $request->page,
                'per_page' => $request->perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'has_next_page' => $request->page < $totalPages,
                'has_previous_page' => $request->page > 1
            ],
            'filters' => $request->getFilters(),
            'sorting' => [
                'sort_by' => $request->sortBy,
                'sort_direction' => $request->sortDirection
            ]
        ];
    }
}
