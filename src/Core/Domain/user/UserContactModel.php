<?php

namespace itaxcix\Core\Domain\user;

class UserContactModel {
    private int $id;
    private UserModel $user;
    private ContactTypeModel $type;
    private string $value;
    private bool $confirmed;
    private bool $active = true;
}