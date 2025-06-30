<?php

namespace itaxcix\Core\UseCases\TucStatus;

use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusResponseDTO;

class TucStatusListUseCase
{
    private TucStatusRepositoryInterface $repository;

    public function __construct(TucStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TucStatusPaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $tucStatuses = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($tucStatus) {
            return new TucStatusResponseDTO(
                id: $tucStatus->getId(),
                name: $tucStatus->getName(),
                active: $tucStatus->isActive()
            );
        }, $tucStatuses);

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

