<?php

namespace itaxcix\Core\Domain\vehicle;

use itaxcix\Infrastructure\Database\Entity\vehicle\ServiceRouteEntity;

class ServiceRouteModel {
    private int $id;
    private ?TucProcedureModel $procedure = null;
    private ?ServiceTypeModel $serviceType = null;
    private ?string $text = null;
    private bool $active = true;

    /**
     * @param int $id
     * @param TucProcedureModel|null $procedure
     * @param ServiceTypeModel|null $serviceType
     * @param string|null $text
     * @param bool $active
     */
    public function __construct(int $id, ?TucProcedureModel $procedure, ?ServiceTypeModel $serviceType, ?string $text, bool $active)
    {
        $this->id = $id;
        $this->procedure = $procedure;
        $this->serviceType = $serviceType;
        $this->text = $text;
        $this->active = $active;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getProcedure(): ?TucProcedureModel
    {
        return $this->procedure;
    }

    public function setProcedure(?TucProcedureModel $procedure): void
    {
        $this->procedure = $procedure;
    }

    public function getServiceType(): ?ServiceTypeModel
    {
        return $this->serviceType;
    }

    public function setServiceType(?ServiceTypeModel $serviceType): void
    {
        $this->serviceType = $serviceType;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function toEntity(): ServiceRouteEntity
    {
        $entity = new ServiceRouteEntity();
        $entity->setId($this->id);
        $entity->setProcedure($this->procedure?->toEntity());
        $entity->setServiceType($this->serviceType?->toEntity());
        $entity->setText($this->text);
        $entity->setActive($this->active);
        return $entity;
    }
}