<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Domain\user\UserRoleModel;

interface UserRoleRepositoryInterface
{
    public function findRolesByUserId(int $userId, bool $web): ?array;
    public function saveUserRole(UserRoleModel $userRole): UserRoleModel;
    public function findUserRoleById(int $id): ?UserRoleModel;
    public function findUserRolesByUserId(int $userId): array;
    public function findActiveRolesByUserId(int $userId): array;
    public function findAllUserRoles(): array;
    public function findByUserAndRole(UserModel $user, RoleModel $role): ?UserRoleModel;
    public function assignRoleToUser(UserModel $user, RoleModel $role): UserRoleModel;
    public function removeRoleFromUser(int $userId, int $roleId): bool;
    public function deleteUserRole(UserRoleModel $userRole): bool;
    public function removeAllByUserId(int $userId): bool;
    public function updateUserRoles(int $userId, array $roleIds): bool;
    public function hasActiveUsersByRoleId(int $roleId): bool;
    public function toDomain(object $entity): UserRoleModel;
}
