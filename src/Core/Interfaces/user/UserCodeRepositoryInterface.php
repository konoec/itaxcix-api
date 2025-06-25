<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserCodeModel;

interface UserCodeRepositoryInterface
{
    public function findUserCodeByValueAndUser(string $code, int $userId): ?UserCodeModel;
    public function findLatestUnusedCodeByContact(int $contactId): ?UserCodeModel;
    public function saveUserCode(UserCodeModel $code): UserCodeModel;
    public function findUserCodeByUserIdAndTypeId(int $userId, int $typeId): ?UserCodeModel;
}
