<?php

namespace itaxcix\Core\UseCases\Admin\Role;

use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Role\ListRolesRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\ListRolesResponseDTO;

class ListRolesUseCase
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(ListRolesRequestDTO $request): ListRolesResponseDTO
    {
        $roles = $this->roleRepository->findAllPaginated(
            page: $request->page,
            limit: $request->limit,
            search: $request->search,
            webOnly: $request->webOnly,
            activeOnly: $request->activeOnly
        );

        $total = $this->roleRepository->countAll(
            search: $request->search,
            webOnly: $request->webOnly,
            activeOnly: $request->activeOnly
        );

        return new ListRolesResponseDTO(
            roles: $roles,
            total: $total,
            page: $request->page,
            limit: $request->limit,
            totalPages: ceil($total / $request->limit)
        );
    }
}
