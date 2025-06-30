<?php

namespace itaxcix\Core\UseCases\UserCodeType;

use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeResponseDTO;

class UserCodeTypeListUseCase
{
    private UserCodeTypeRepositoryInterface $repository;

    public function __construct(UserCodeTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserCodeTypePaginationRequestDTO $request): array
    {
        $filters = $request->getFilters();

        $userCodeTypes = $this->repository->findAll(
            $filters,
            $request->page,
            $request->perPage,
            $request->sortBy,
            $request->sortDirection
        );

        $totalItems = $this->repository->countAll($filters);
        $totalPages = (int) ceil($totalItems / $request->perPage);

        $data = array_map(function ($userCodeType) {
            return new UserCodeTypeResponseDTO(
                id: $userCodeType->getId(),
                name: $userCodeType->getName(),
                active: $userCodeType->isActive()
            );
        }, $userCodeTypes);

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

