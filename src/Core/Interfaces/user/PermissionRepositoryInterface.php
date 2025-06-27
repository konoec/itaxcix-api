<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\PermissionModel;

interface PermissionRepositoryInterface
{
    public function findPermissionById(int $id): ?PermissionModel;
    public function findPermissionByName(string $name): ?PermissionModel;
    public function findAllPermissions(): array;
    public function findAllPermissionsPaginated(int $page, int $perPage): object;
    public function savePermission(PermissionModel $permission): PermissionModel;
    public function deletePermission(PermissionModel $permission): void;
}
