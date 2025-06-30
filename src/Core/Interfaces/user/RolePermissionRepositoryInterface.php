<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\RolePermissionModel;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Domain\user\PermissionModel;

interface RolePermissionRepositoryInterface
{
    public function findRolePermissionById(int $id): ?RolePermissionModel;
    public function findByRoleAndPermission(int $roleId, int $permissionId): ?RolePermissionModel;
    public function findAllRolePermissions(): array;
    public function hasActiveRolesByPermissionId(int $permissionId): bool;
    public function saveRolePermission(RolePermissionModel $rolePermission): RolePermissionModel;
    public function deleteRolePermission(RolePermissionModel $rolePermission): void;

    // Nuevos métodos para administración avanzada
    public function removeAllByRoleId(int $roleId): void;
    public function assignPermissionToRole(RoleModel $role, PermissionModel $permission): RolePermissionModel;
    public function findPermissionsByRoleId(int $roleId): array;
}
