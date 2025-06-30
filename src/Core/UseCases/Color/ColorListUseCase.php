<?php

namespace itaxcix\Core\UseCases\Color;

use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Color\ColorPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Color\ColorResponseDTO;

class ColorListUseCase
{
    private ColorRepositoryInterface $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function execute(ColorPaginationRequestDTO $request): array
    {
        $colors = $this->colorRepository->findWithPagination(
            $request->page,
            $request->perPage,
            $request->getFilters(),
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->colorRepository->getTotalCount($request->getFilters());
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $colorDTOs = array_map(function($color) {
            return ColorResponseDTO::fromModel($color);
        }, $colors);

        return [
            'data' => array_map(fn($dto) => $dto->toArray(), $colorDTOs),
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
