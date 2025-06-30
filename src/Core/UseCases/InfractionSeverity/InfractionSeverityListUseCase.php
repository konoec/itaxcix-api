<?php

namespace itaxcix\Core\UseCases\InfractionSeverity;

use itaxcix\Core\Interfaces\infraction\InfractionSeverityRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityResponseDTO;

class InfractionSeverityListUseCase
{
    private InfractionSeverityRepositoryInterface $repository;

    public function __construct(InfractionSeverityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(InfractionSeverityPaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $infractionSeverities = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($infractionSeverity) {
            return new InfractionSeverityResponseDTO(
                id: $infractionSeverity->getId(),
                name: $infractionSeverity->getName(),
                active: $infractionSeverity->isActive()
            );
        }, $infractionSeverities);

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

