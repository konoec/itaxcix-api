<?php

namespace itaxcix\Core\Domain\travel;

use DateTime;
use itaxcix\Core\Domain\location\CoordinatesModel;
use itaxcix\Core\Domain\user\UserModel;

class TravelModel {
    private int $id;
    private ?UserModel $citizen = null;
    private ?UserModel $driver = null;
    private ?CoordinatesModel $origin = null;
    private ?CoordinatesModel $destination = null;
    private ?DateTime $startDate = null;
    private ?DateTime $endDate = null;
    private DateTime $creationDate;
    private TravelStatusModel $status;
}