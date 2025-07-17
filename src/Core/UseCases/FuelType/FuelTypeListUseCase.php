<?php

namespace itaxcix\Core\UseCases\FuelType;

use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeResponseDTO;

class FuelTypeListUseCase
{
    private FuelTypeRepositoryInterface $fuelTypeRepository;

    public function __construct(FuelTypeRepositoryInterface $fuelTypeRepository)
    {
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function execute(FuelTypePaginationRequestDTO $request): array
    {
        $fuelTypes = $this->fuelTypeRepository->findWithPagination(
            $request->page,
            $request->perPage,
            $request->getFilters(),
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->fuelTypeRepository->getTotalCount($request->getFilters());
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $fuelTypeDTOs = array_map(function($fuelType) {
            return FuelTypeResponseDTO::fromModel($fuelType);
        }, $fuelTypes);

        return [
            'data' => array_map(fn($dto) => $dto->toArray(), $fuelTypeDTOs),
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
