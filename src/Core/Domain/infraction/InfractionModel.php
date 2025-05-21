<?php

namespace itaxcix\Core\Domain\infraction;

use DateTime;
use itaxcix\Core\Domain\user\UserModel;

class InfractionModel {
    private int $id;
    private ?UserModel $user = null;
    private ?InfractionSeverityModel $severity = null;
    private DateTime $date;
    private ?string $description = null;
    private ?InfractionStatusModel $status = null;
}