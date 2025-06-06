<?php

namespace itaxcix\Core\Domain\user;

class CitizenProfileModel {
    private ?int $id;
    private ?UserModel $user = null;
    private float $averageRating = 0.00;
    private int $ratingCount = 0;

    /**
     * @param ?int $id
     * @param UserModel|null $user
     * @param float $averageRating
     * @param int $ratingCount
     */
    public function __construct(?int $id, ?UserModel $user, float $averageRating, int $ratingCount)
    {
        $this->id = $id;
        $this->user = $user;
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