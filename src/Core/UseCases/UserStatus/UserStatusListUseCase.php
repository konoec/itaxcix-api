<?php

namespace itaxcix\Core\UseCases\UserStatus;

use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusPaginationRequestDTO;

class UserStatusListUseCase
{
    private UserStatusRepositoryInterface $repository;

    public function __construct(UserStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserStatusPaginationRequestDTO $request): array
    {
        $userStatuses = $this->repository->findAll($request);
        $total = $this->repository->count($request);

        return [
            'items' => array_map(fn($userStatus) => [
                'id' => $userStatus->getId(),
                'name' => $userStatus->getName(),
                'active' => $userStatus->isActive()
            ], $userStatuses),
            'pagination' => [
                'page' => $request->getPage(),
                'perPage' => $request->getPerPage(),
                'total' => $total,
                'totalPages' => ceil($total / $request->getPerPage())
            ]
        ];
    }
}
