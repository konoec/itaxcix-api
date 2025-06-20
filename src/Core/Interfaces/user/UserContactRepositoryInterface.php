<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserContactModel;

interface UserContactRepositoryInterface {
    public function findAllUserContactByValue(string $value): ?UserContactModel;
    public function findUserContactByUserId(int $userId): ?UserContactModel;
    public function findUserContactByUserIdAndContactTypeId(int $userId, int $contactTypeId): ?UserContactModel;
    public function saveUserContact(UserContactModel $userContactModel): UserContactModel;
}