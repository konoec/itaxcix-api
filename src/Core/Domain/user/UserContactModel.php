<?php

namespace itaxcix\Core\Domain\user;

use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;

class UserContactModel {
    private ?int $id;
    private ?UserModel $user;
    private ?ContactTypeModel $type;
    private string $value;
    private bool $confirmed;
    private bool $active = true;

    /**
     * @param ?int $id
     * @param ?UserModel $user
     * @param ?ContactTypeModel $type
     * @param string $value
     * @param bool $confirmed
     * @param bool $active
     */
    public function __construct(?int $id, ?UserModel $user, ?ContactTypeModel $type, string $value, bool $confirmed, bool $active)
    {
        $this->id = $id;
        $this->user = $user;
        $this->type = $type;
        $this->value = $value;
        $this->confirmed = $confirmed;
        $this->active = $active;
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

    public function setUser(UserModel $user): void
    {
        $this->user = $user;
    }

    public function getType(): ?ContactTypeModel
    {
        return $this->type;
    }

    public function setType(ContactTypeModel $type): void
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function toEntity(): UserContactEntity
    {
        $entity = new UserContactEntity();
        $entity->setId($this->id);
        $entity->setUser($this->user->toEntity());
        $entity->setType($this->type->toEntity());
        $entity->setValue($this->value);
        $entity->setConfirmed($this->confirmed);
        $entity->setActive($this->active);

        return $entity;
    }
}