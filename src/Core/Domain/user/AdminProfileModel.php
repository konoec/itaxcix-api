<?php

namespace itaxcix\Core\Domain\user;

class AdminProfileModel {
    private int $id;
    private ?UserModel $user = null;
    private ?string $area = null;
    private ?string $position = null;
}