<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\ContactTypeModel;

interface ContactTypeRepositoryInterface {
    public function findContactTypeById(int $id): ?ContactTypeModel;
}