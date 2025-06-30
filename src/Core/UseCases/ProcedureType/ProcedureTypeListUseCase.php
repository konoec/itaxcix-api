<?php

namespace itaxcix\Core\UseCases\ProcedureType;

use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeResponseDTO;

class ProcedureTypeListUseCase
{
    private ProcedureTypeRepositoryInterface $repository;

    public function __construct(ProcedureTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ProcedureTypePaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $procedureTypes = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($procedureType) {
            return new ProcedureTypeResponseDTO(
                id: $procedureType->getId(),
                name: $procedureType->getName(),
                active: $procedureType->isActive()
            );
        }, $procedureTypes);

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

