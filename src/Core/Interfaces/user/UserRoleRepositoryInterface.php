<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserRoleModel;

interface UserRoleRepositoryInterface
{
    public function findUserRoleById(int $id): ?UserRoleModel;
    public function findByUserAndRole(int $userId, int $roleId): ?UserRoleModel;
    public function findAllUserRoles(): array;
    public function hasActiveUsersByRoleId(int $roleId): bool;
    public function saveUserRole(UserRoleModel $userRole): UserRoleModel;
    public function deleteUserRole(UserRoleModel $userRole): void;
}
