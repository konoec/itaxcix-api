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
            'items' => array_map(fn($dto) => $dto->toArray(), $colorDTOs),
            'meta' => [
                'total' => $totalItems,
                'perPage' => $request->perPage,
                'currentPage' => $request->page,
                'lastPage' => $totalPages,
                'search' => $request->search ?? null,
                'filters' => $request->getFilters(),
                'sortBy' => $request->sortBy,
                'sortDirection' => $request->sortDirection
            ]
        ];
    }
}
