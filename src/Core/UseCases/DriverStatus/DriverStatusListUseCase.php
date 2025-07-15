<?php

namespace itaxcix\Core\UseCases\DriverStatus;

use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusResponseDTO;

class DriverStatusListUseCase
{
    private DriverStatusRepositoryInterface $repository;

    public function __construct(DriverStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DriverStatusPaginationRequestDTO $request): array
    {
        // Obtener elementos paginados
        $items = $this->repository->findWithFilters(
            search: $request->search,
            name: $request->name,
            active: $request->active,
            sortBy: $request->sortBy,
            sortDirection: $request->sortDirection,
            page: $request->page,
            perPage: $request->perPage,
            onlyActive: $request->onlyActive
        );

        // Contar total de elementos
        $total = $this->repository->countWithFilters(
            search: $request->search,
            name: $request->name,
            active: $request->active,
            onlyActive: $request->onlyActive
        );

        // Convertir a DTOs de respuesta
        $responseItems = array_map(
            fn($item) => DriverStatusResponseDTO::fromModel($item),
            $items
        );

        // Calcular metadatos de paginación
        $lastPage = $request->perPage > 0 ? (int)ceil($total / $request->perPage) : 1;

        return [
            'items' => $responseItems,
            'meta' => [
                'total' => $total,
                'perPage' => $request->perPage,
                'currentPage' => $request->page,
                'lastPage' => $lastPage,
                'search' => $request->search,
                'filters' => [
                    'name' => $request->name,
                    'active' => $request->active,
                    'onlyActive' => $request->onlyActive
                ],
                'sortBy' => $request->sortBy,
                'sortDirection' => $request->sortDirection
            ]
        ];
    }
}
