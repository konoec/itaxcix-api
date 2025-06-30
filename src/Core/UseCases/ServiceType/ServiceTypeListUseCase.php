<?php

namespace itaxcix\Core\UseCases\ServiceType;

use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeResponseDTO;

class ServiceTypeListUseCase
{
    private ServiceTypeRepositoryInterface $repository;

    public function __construct(ServiceTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ServiceTypePaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $serviceTypes = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($serviceType) {
            return new ServiceTypeResponseDTO(
                id: $serviceType->getId(),
                name: $serviceType->getName(),
                active: $serviceType->isActive()
            );
        }, $serviceTypes);

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

