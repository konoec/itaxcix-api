<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\PermissionModel;

interface PermissionRepositoryInterface
{
    public function findPermissionById(int $id): ?PermissionModel;
    public function findPermissionByName(string $name): ?PermissionModel;
    public function findAllPermissions(): array;
    public function savePermission(PermissionModel $permission): PermissionModel;
    public function deletePermission(PermissionModel $permission): void;

    // Nuevos métodos para administración avanzada
    public function findById(int $id): ?PermissionModel;
    public function findAllPaginated(
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?bool $webOnly = null,
        ?bool $activeOnly = true
    ): array;
    public function countAll(
        ?string $search = null,
        ?bool $webOnly = null,
        ?bool $activeOnly = true
    ): int;
}
