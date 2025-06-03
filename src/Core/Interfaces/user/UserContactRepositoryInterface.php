<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserContactModel;

interface UserContactRepositoryInterface {
    public function findAllUserContactByValue(string $value): ?UserContactModel;
    public function findUserContactById(int $id): ?UserContactModel;
    public function saveUserContact(UserContactModel $userContactModel): UserContactModel;
}