<?php

namespace itaxcix\Core\UseCases\TravelStatus;

use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusResponseDTO;

class TravelStatusListUseCase
{
    private TravelStatusRepositoryInterface $repository;

    public function __construct(TravelStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TravelStatusPaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $travelStatuses = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($travelStatus) {
            return new TravelStatusResponseDTO(
                id: $travelStatus->getId(),
                name: $travelStatus->getName(),
                active: $travelStatus->isActive()
            );
        }, $travelStatuses);

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

