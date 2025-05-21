<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class UserModel {
    private int $id;
    private string $alias;
    private string $password;
    private ?PersonModel $person = null;
    private ?UserStatusModel $status = null;

    /**
     * @param int $id
     * @param string $alias
     * @param string $password
     * @param PersonModel|null $person
     * @param UserStatusModel|null $status
     */
    public function __construct(int $id, string $alias, string $password, ?PersonModel $person, ?UserStatusModel $status)
    {
        $this->id = $id;
        $this->alias = $alias;
        $this->password = $password;
        $this->person = $person;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
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

    public function toEntity(): UserEntity {
        $entity = new UserEntity();

        $entity->setId($this->id);

        $entity->setAlias($this->alias);
        $entity->setPassword($this->password);

        if ($this->person !== null) {
            $entity->setPerson($this->person->toEntity());
        }

        if ($this->status !== null) {
            $entity->setStatus($this->status->toEntity());
        }

        return $entity;
    }
}