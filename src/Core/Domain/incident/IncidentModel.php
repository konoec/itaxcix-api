<?php

namespace itaxcix\Core\Domain\incident;

use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Core\Domain\user\UserModel;

class IncidentModel {
    private ?int $id = null;
    private ?UserModel $user = null;
    private ?TravelModel $travel = null;
    private ?IncidentTypeModel $type = null;
    private ?string $comment = null;
    private bool $active = true;

    public function __construct(
        ?int $id = null,
        ?UserModel $user = null,
        ?TravelModel $travel = null,
        ?IncidentTypeModel $type = null,
        ?string $comment = null,
        bool $active = true
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->travel = $travel;
        $this->type = $type;
        $this->comment = $comment;
        $this->active = $active;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUser(): ?UserModel { return $this->user; }
    public function setUser(?UserModel $user): void { $this->user = $user; }

    public function getTravel(): ?TravelModel { return $this->travel; }
    public function setTravel(?TravelModel $travel): void { $this->travel = $travel; }

    public function getType(): ?IncidentTypeModel { return $this->type; }
    public function setType(?IncidentTypeModel $type): void { $this->type = $type; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): void { $this->comment = $comment; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): void { $this->active = $active; }
}