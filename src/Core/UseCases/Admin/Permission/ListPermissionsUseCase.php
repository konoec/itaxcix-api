<?php

namespace itaxcix\Core\UseCases\Admin\Permission;

use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Permission\ListPermissionsRequestDTO;
use itaxcix\Shared\DTO\Admin\Permission\ListPermissionsResponseDTO;

class ListPermissionsUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(ListPermissionsRequestDTO $request): ListPermissionsResponseDTO
    {
        $permissions = $this->permissionRepository->findAllPaginated(
            page: $request->page,
            limit: $request->limit,
            search: $request->search,
            webOnly: $request->webOnly,
            activeOnly: $request->activeOnly
        );

        $total = $this->permissionRepository->countAll(
            search: $request->search,
            webOnly: $request->webOnly,
            activeOnly: $request->activeOnly
        );

        return new ListPermissionsResponseDTO(
            permissions: $permissions,
            total: $total,
            page: $request->page,
            limit: $request->limit,
            totalPages: ceil($total / $request->limit)
        );
    }
}
