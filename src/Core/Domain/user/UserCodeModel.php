<?php

namespace itaxcix\Core\Domain\user;

use DateTime;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeEntity;

class UserCodeModel {
    private int $id;
    private ?UserCodeTypeModel $type = null;
    private ?UserContactModel $contact = null;
    private string $code;
    private DateTime $expirationDate;
    private ?DateTime $useDate = null;
    private bool $used = false;

    /**
     * @param int $id
     * @param UserCodeTypeModel|null $type
     * @param UserContactModel|null $contact
     * @param string $code
     * @param DateTime $expirationDate
     * @param DateTime|null $useDate
     * @param bool $used
     */
    public function __construct(int $id, ?UserCodeTypeModel $type, ?UserContactModel $contact, string $code, DateTime $expirationDate, ?DateTime $useDate, bool $used)
    {
        $this->id = $id;
        $this->type = $type;
        $this->contact = $contact;
        $this->code = $code;
        $this->expirationDate = $expirationDate;
        $this->useDate = $useDate;
        $this->used = $used;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): ?UserCodeTypeModel
    {
        return $this->type;
    }

    public function setType(?UserCodeTypeModel $type): void
    {
        $this->type = $type;
    }

    public function getContact(): ?UserContactModel
    {
        return $this->contact;
    }

    public function setContact(?UserContactModel $contact): void
    {
        $this->contact = $contact;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getUseDate(): ?DateTime
    {
        return $this->useDate;
    }

    public function setUseDate(?DateTime $useDate): void
    {
        $this->useDate = $useDate;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }

    public function toEntity(): UserCodeEntity {
        $entity = new UserCodeEntity();
        $entity->setId($this->getId());
        $entity->setType($this->getType()?->toEntity());
        $entity->setContact($this->getContact()?->toEntity());
        $entity->setCode($this->getCode());
        $entity->setExpirationDate($this->getExpirationDate());
        $entity->setUseDate($this->getUseDate());
        $entity->setUsed($this->isUsed());

        return $entity;
    }
}