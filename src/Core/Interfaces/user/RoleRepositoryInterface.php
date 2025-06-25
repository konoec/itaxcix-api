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
}
