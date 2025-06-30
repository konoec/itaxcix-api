<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\RoleModel;

interface RoleRepositoryInterface
{
    public function findRoleById(int $id): ?RoleModel;
    public function findRoleByName(string $name): ?RoleModel;
    public function findAllRoles(): array;
    public function saveRole(RoleModel $role): RoleModel;
    public function deleteRole(RoleModel $role): void;

    // Nuevos métodos para administración avanzada
    public function findById(int $id): ?RoleModel;
    public function save(RoleModel $role): RoleModel;
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
