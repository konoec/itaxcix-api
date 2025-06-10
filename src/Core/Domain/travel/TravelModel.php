<?php

namespace itaxcix\Core\Domain\travel;

use DateTime;
use itaxcix\Core\Domain\location\CoordinatesModel;
use itaxcix\Core\Domain\user\UserModel;

class TravelModel {
    private ?int $id;
    private ?UserModel $citizen = null;
    private ?UserModel $driver = null;
    private ?CoordinatesModel $origin = null;
    private ?CoordinatesModel $destination = null;
    private ?DateTime $startDate = null;
    private ?DateTime $endDate = null;
    private DateTime $creationDate;
    private TravelStatusModel $status;

    /**
     * @param int|null $id
     * @param UserModel|null $citizen
     * @param UserModel|null $driver
     * @param CoordinatesModel|null $origin
     * @param CoordinatesModel|null $destination
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param DateTime $creationDate
     * @param TravelStatusModel $status
     */
    public function __construct(?int $id, ?UserModel $citizen, ?UserModel $driver, ?CoordinatesModel $origin, ?CoordinatesModel $destination, ?DateTime $startDate, ?DateTime $endDate, DateTime $creationDate, TravelStatusModel $status)
    {
        $this->id = $id;
        $this->citizen = $citizen;
        $this->driver = $driver;
        $this->origin = $origin;
        $this->destination = $destination;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->creationDate = $creationDate;
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

    public function getCitizen(): ?UserModel
    {
        return $this->citizen;
    }

    public function setCitizen(?UserModel $citizen): void
    {
        $this->citizen = $citizen;
    }

    public function getDriver(): ?UserModel
    {
        return $this->driver;
    }

    public function setDriver(?UserModel $driver): void
    {
        $this->driver = $driver;
    }

    public function getOrigin(): ?CoordinatesModel
    {
        return $this->origin;
    }

    public function setOrigin(?CoordinatesModel $origin): void
    {
        $this->origin = $origin;
    }

    public function getDestination(): ?CoordinatesModel
    {
        return $this->destination;
    }

    public function setDestination(?CoordinatesModel $destination): void
    {
        $this->destination = $destination;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTime $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    public function getStatus(): TravelStatusModel
    {
        return $this->status;
    }

    public function setStatus(TravelStatusModel $status): void
    {
        $this->status = $status;
    }


}