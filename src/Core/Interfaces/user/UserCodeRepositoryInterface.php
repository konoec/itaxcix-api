<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserCodeModel;

interface UserCodeRepositoryInterface {
    public function findUserCodeByValueAndUser(string $value, int $userId): ?UserCodeModel;
    public function findUserCodeByUserIdAndTypeId(int $userId, int $typeId): ?UserCodeModel;
    public function saveUserCode(UserCodeModel $userCodeModel): UserCodeModel;
}