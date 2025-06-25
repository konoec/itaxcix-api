<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RolePermissionListResponseDTO;

interface RolePermissionListUseCase
{
    public function execute(int $page, int $perPage): ?RolePermissionListResponseDTO;
}
