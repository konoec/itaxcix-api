<?php

namespace itaxcix\Core\Domain\infraction;

use DateTime;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Infrastructure\Database\Entity\infraction\InfractionEntity;

class InfractionModel {
    private int $id;
    private ?UserModel $user = null;
    private ?InfractionSeverityModel $severity = null;
    private DateTime $date;
    private ?string $description = null;
    private ?InfractionStatusModel $status = null;

    public function __construct(int $id, ?UserModel $user, ?InfractionSeverityModel $severity, DateTime $date, ?string $description, ?InfractionStatusModel $status)
    {
        $this->id = $id;
        $this->user = $user;
        $this->severity = $severity;
        $this->date = $date;
        $this->description = $description;
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

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(?UserModel $user): void
    {
        $this->user = $user;
    }

    public function getSeverity(): ?InfractionSeverityModel
    {
        return $this->severity;
    }

    public function setSeverity(?InfractionSeverityModel $severity): void
    {
        $this->severity = $severity;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): ?InfractionStatusModel
    {
        return $this->status;
    }

    public function setStatus(?InfractionStatusModel $status): void
    {
        $this->status = $status;
    }

    public function toEntity(): InfractionEntity
    {
        $entity = new InfractionEntity();
        $entity->setId($this->id);
        $entity->setUser($this->user?->toEntity());
        $entity->setSeverity($this->severity?->toEntity());
        $entity->setDate($this->date);
        $entity->setDescription($this->description);
        $entity->setStatus($this->status?->toEntity());

        return $entity;
    }
}