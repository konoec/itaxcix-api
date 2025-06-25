<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserCodeTypeModel;

interface UserCodeTypeRepositoryInterface
{
    public function findUserCodeTypeByName(string $name): ?UserCodeTypeModel;
    public function findUserCodeTypeById(int $id): ?UserCodeTypeModel;
}
