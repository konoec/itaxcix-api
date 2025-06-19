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

    /**
     * @param int $id
     * @param UserModel|null $rater
     * @param UserModel|null $rated
     * @param TravelModel|null $travel
     * @param int $score
     * @param string|null $comment
     */
    public function __construct(int $id, ?UserModel $rater, ?UserModel $rated, ?TravelModel $travel, int $score, ?string $comment)
    {
        $this->id = $id;
        $this->rater = $rater;
        $this->rated = $rated;
        $this->travel = $travel;
        $this->score = $score;
        $this->comment = $comment;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRater(): ?UserModel
    {
        return $this->rater;
    }

    public function setRater(?UserModel $rater): void
    {
        $this->rater = $rater;
    }

    public function getRated(): ?UserModel
    {
        return $this->rated;
    }

    public function setRated(?UserModel $rated): void
    {
        $this->rated = $rated;
    }

    public function getTravel(): ?TravelModel
    {
        return $this->travel;
    }

    public function setTravel(?TravelModel $travel): void
    {
        $this->travel = $travel;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

}