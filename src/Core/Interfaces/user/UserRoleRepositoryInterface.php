<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserRoleModel;

interface UserRoleRepositoryInterface
{
    public function findRolesByUserId(int $userId, bool $web): ?array;
    public function saveUserRole(UserRoleModel $userRoleModel): UserRoleModel;
}