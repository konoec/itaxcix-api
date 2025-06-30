<?php

namespace itaxcix\Core\UseCases\InfractionStatus;

use itaxcix\Core\Interfaces\infraction\InfractionStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusResponseDTO;

class InfractionStatusListUseCase
{
    private InfractionStatusRepositoryInterface $repository;

    public function __construct(InfractionStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(InfractionStatusPaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $infractionStatuses = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($infractionStatus) {
            return new InfractionStatusResponseDTO(
                id: $infractionStatus->getId(),
                name: $infractionStatus->getName(),
                active: $infractionStatus->isActive()
            );
        }, $infractionStatuses);

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

