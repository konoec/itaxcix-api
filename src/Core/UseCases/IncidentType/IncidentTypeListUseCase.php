<?php

namespace itaxcix\Core\UseCases\IncidentType;

use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentType\IncidentTypeResponseDTO;

class IncidentTypeListUseCase
{
    private IncidentTypeRepositoryInterface $repository;

    public function __construct(IncidentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(IncidentTypePaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $incidentTypes = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($incidentType) {
            return new IncidentTypeResponseDTO(
                id: $incidentType->getId(),
                name: $incidentType->getName(),
                active: $incidentType->isActive()
            );
        }, $incidentTypes);

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

