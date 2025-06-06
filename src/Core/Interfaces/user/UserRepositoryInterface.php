<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserModel;

interface UserRepositoryInterface {
    public function findUserByPersonDocument(string $document): ?UserModel;
    public function findAllUserByPersonDocument(string $document): ?UserModel;
    public function findAllUserByPersonId(int $personId): ?UserModel;
    public function findUserById(int $id): ?UserModel;
    public function saveUser(UserModel $userModel): UserModel;
}