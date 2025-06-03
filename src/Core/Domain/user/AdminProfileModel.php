<?php

namespace itaxcix\Core\Domain\user;

class AdminProfileModel {
    private ?int $id;
    private ?UserModel $user = null;
    private ?string $area = null;
    private ?string $position = null;

    public function __construct(?int $id = null, ?UserModel $user = null, ?string $area = null, ?string $position = null)
    {
        $this->id = $id;
        $this->user = $user;
        $this->area = $area;
        $this->position = $position;
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

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): void
    {
        $this->area = $area;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }


}