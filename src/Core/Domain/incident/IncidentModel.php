<?php

namespace itaxcix\Core\Domain\incident;

use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Core\Domain\user\UserModel;

class IncidentModel {
    private ?int $id = null;
    private ?UserModel $user = null;
    private ?TravelModel $travel = null;
    private ?IncidentTypeModel $type = null;
    private ?string $comment = null;
    private bool $active = true;
}