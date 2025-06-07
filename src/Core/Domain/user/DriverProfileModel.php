<?php

namespace itaxcix\Core\Domain\user;

class DriverProfileModel {
    private ?int $id;
    private ?UserModel $user = null;
    private ?DriverStatusModel $status;
    private float $averageRating = 0.00;
    private int $ratingCount = 0;

    /**
     * @param int|null $id
     * @param UserModel|null $user
     * @param DriverStatusModel|null $status
     * @param float $averageRating
     * @param int $ratingCount
     */
    public function __construct(?int $id, ?UserModel $user, ?DriverStatusModel $status, float $averageRating, int $ratingCount)
    {
        $this->id = $id;
        $this->user = $user;
        $this->status = $status;
        $this->averageRating = $averageRating;
        $this->ratingCount = $ratingCount;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(?UserModel $user): void
    {
        $this->user = $user;
    }

    public function getStatus(): ?DriverStatusModel
    {
        return $this->status;
    }

    public function setStatus(?DriverStatusModel $status): void
    {
        $this->status = $status;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function setAverageRating(float $averageRating): void
    {
        $this->averageRating = $averageRating;
    }

    public function getRatingCount(): int
    {
        return $this->ratingCount;
    }

    public function setRatingCount(int $ratingCount): void
    {
        $this->ratingCount = $ratingCount;
    }
}