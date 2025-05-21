<?php

namespace itaxcix\Core\Domain\user;

use DateTime;

class UserCodeModel {
    private int $id;
    private ?UserModel $user = null;
    private ?UserCodeTypeModel $type = null;
    private ?UserContactModel $contact = null;
    private string $code;
    private DateTime $expirationDate;
    private ?DateTime $useDate = null;
    private bool $used = false;
}