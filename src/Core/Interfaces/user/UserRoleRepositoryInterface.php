<?php

namespace itaxcix\Core\Interfaces\user;

interface UserRoleRepositoryInterface
{
    public function findRolesByUserId(int $userId, bool $web): ?array;
}