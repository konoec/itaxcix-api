<?php

namespace itaxcix\Core\Domain\user;

class PermissionModel {
    private int $id;
    private string $name;
    private bool $active = true;
}