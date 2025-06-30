<?php

namespace itaxcix\Core\UseCases\VehicleClass;

use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassResponseDTO;

class VehicleClassListUseCase
{
    private VehicleClassRepositoryInterface $repository;

    public function __construct(VehicleClassRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(VehicleClassPaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $vehicleClasses = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($vehicleClass) {
            return new VehicleClassResponseDTO(
                id: $vehicleClass->getId(),
                name: $vehicleClass->getName(),
                active: $vehicleClass->isActive()
            );
        }, $vehicleClasses);

        return [
            'data' => array_map(fn($item) => $item->toArray(), $data),
            'pagination' => [
                'current_page' => $request->page,
                'per_page' => $request->perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'has_next_page' => $request->page < $totalPages,
                'has_previous_page' => $request->page > 1
            ]
        ];
    }
}
