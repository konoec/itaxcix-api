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

        $total = $this->repository->countAll($filters);

        return [
            'items' => array_map(fn($userCodeType) => [
                'id' => $userCodeType->getId(),
                'name' => $userCodeType->getName(),
                'active' => $userCodeType->isActive()
            ], $userCodeTypes),
            'pagination' => [
                'page' => $request->page,
                'perPage' => $request->perPage,
                'total' => $total,
                'totalPages' => ceil($total / $request->perPage)
            ]
        ];
    }
}
