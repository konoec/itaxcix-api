<?php

namespace itaxcix\Core\Domain\user;

class DriverProfileModel {
    private ?int $id;
    private ?UserModel $user = null;
    private bool $available = false;
    private ?DriverStatusModel $status;

    /**
     * @param int|null $id
     * @param UserModel|null $user
     * @param bool $available
     * @param DriverStatusModel|null $status
     */
    public function __construct(?int $id, ?UserModel $user, bool $available, ?DriverStatusModel $status)
    {
        $this->id = $id;
        $this->user = $user;
        $this->available = $available;
        $this->status = $status;
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

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function getStatus(): ?DriverStatusModel
    {
        return $this->status;
    }

    public function setStatus(?DriverStatusModel $status): void
    {
        $this->status = $status;
    }


}