<?php

namespace itaxcix\Core\Domain\user;

class DriverProfileModel {
    private ?int $id;
    private ?UserModel $user = null;
    private bool $available = false;

    /**
     * @param int|null $id
     * @param UserModel|null $user
     * @param bool $available
     */
    public function __construct(?int $id, ?UserModel $user, bool $available)
    {
        $this->id = $id;
        $this->user = $user;
        $this->available = $available;
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
}