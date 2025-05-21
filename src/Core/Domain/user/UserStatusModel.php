<?php

namespace itaxcix\Core\Domain\user;

class UserStatusModel {
    private ?int $id = null;
    private string $name;
    private bool $active = true;
}