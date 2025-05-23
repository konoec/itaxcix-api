<?php

namespace itaxcix\Core\Domain\location;

use itaxcix\Infrastructure\Database\Entity\location\DepartmentEntity;

class DepartmentModel {
    private int $id;
    private ?string $name = null;
    private ?string $ubigeo = null;

    /**
     * @param int $id
     * @param string|null $name
     * @param string|null $ubigeo
     */
    public function __construct(int $id, ?string $name, ?string $ubigeo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ubigeo = $ubigeo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): void
    {
        $this->ubigeo = $ubigeo;
    }

    public function toEntity(): DepartmentEntity
    {
        $departmentEntity = new DepartmentEntity();
        $departmentEntity->setId($this->id);
        $departmentEntity->setName($this->name);
        $departmentEntity->setUbigeo($this->ubigeo);

        return $departmentEntity;
    }
}