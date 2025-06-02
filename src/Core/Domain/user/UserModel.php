<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class UserModel {
    private ?int $id;
    private string $password;
    private ?PersonModel $person;
    private ?UserStatusModel $status;

    public function __construct(?int $id, string $password, ?PersonModel $person, ?UserStatusModel $status)
    {
        $this->id = $id;
        $this->password = $password;
        $this->person = $person;
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
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPerson(): ?PersonModel
    {
        return $this->person;
    }

    public function setPerson(?PersonModel $person): void
    {
        $this->person = $person;
    }

    public function getStatus(): ?UserStatusModel
    {
        return $this->status;
    }

    public function setStatus(?UserStatusModel $status): void
    {
        $this->status = $status;
    }

    public function toEntity(?UserEntity $existing = null): UserEntity {
        $entity = $existing ?? new UserEntity();

        $entity->setId($this->getId());
        $entity->setPassword($this->getPassword());
        $entity->setPerson($this->getPerson()?->toEntity());
        $entity->setStatus($this->getStatus()?->toEntity());

        return $entity;
    }
}