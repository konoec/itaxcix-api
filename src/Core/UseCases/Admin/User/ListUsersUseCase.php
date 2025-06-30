<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\ListUsersRequestDTO;
use itaxcix\Shared\DTO\Admin\User\ListUsersResponseDTO;

class ListUsersUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(ListUsersRequestDTO $request): ListUsersResponseDTO
    {
        $users = $this->userRepository->findAllPaginated(
            page: $request->page,
            limit: $request->limit,
            search: $request->search,
            roleId: $request->roleId,
            statusId: $request->statusId,
            withWebAccess: $request->withWebAccess
        );

        $total = $this->userRepository->countAll(
            search: $request->search,
            roleId: $request->roleId,
            statusId: $request->statusId,
            withWebAccess: $request->withWebAccess
        );

        // Enriquecer usuarios con sus roles
        $enrichedUsers = [];
        foreach ($users as $user) {
            $userRoles = $this->userRoleRepository->findActiveRolesByUserId($user->getId());
            $enrichedUsers[] = [
                'user' => $user,
                'roles' => $userRoles
            ];
        }

        return new ListUsersResponseDTO(
            users: $enrichedUsers,
            total: $total,
            page: $request->page,
            limit: $request->limit,
            totalPages: ceil($total / $request->limit)
        );
    }
}
