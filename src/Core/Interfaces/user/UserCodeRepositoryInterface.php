<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserCodeModel;

interface UserCodeRepositoryInterface {
    public function saveUserCode(UserCodeModel $userCodeModel): UserCodeModel;
}