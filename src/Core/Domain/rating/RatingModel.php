<?php

namespace itaxcix\Core\Domain\rating;

use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Core\Domain\user\UserModel;

class RatingModel {
    private int $id;
    private ?UserModel $rater = null;
    private ?UserModel $rated = null;
    private ?TravelModel $travel = null;
    private int $score;
    private ?string $comment = null;
}