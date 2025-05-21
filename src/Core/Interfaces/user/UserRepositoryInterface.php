<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserModel;

interface UserRepositoryInterface {
    public function save(UserModel $user): void;
    public function ofId(int $id): ?UserModel;
    public function findByAlias(string $alias): ?UserModel;
}