<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\RolePermissionModel;

interface RolePermissionRepositoryInterface
{
    public function findRolePermissionById(int $id): ?RolePermissionModel;
    public function findByRoleAndPermission(int $roleId, int $permissionId): ?RolePermissionModel;
    public function findAllRolePermissions(): array;
    public function hasActiveRolesByPermissionId(int $permissionId): bool;
    public function saveRolePermission(RolePermissionModel $rolePermission): RolePermissionModel;
    public function deleteRolePermission(RolePermissionModel $rolePermission): void;
}
