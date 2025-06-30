<?php

namespace itaxcix\Core\UseCases\TucModality;

use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityResponseDTO;

class TucModalityListUseCase
{
    private TucModalityRepositoryInterface $repository;

    public function __construct(TucModalityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TucModalityPaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $tucModalities = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($tucModality) {
            return new TucModalityResponseDTO(
                id: $tucModality->getId(),
                name: $tucModality->getName(),
                active: $tucModality->isActive()
            );
        }, $tucModalities);

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

