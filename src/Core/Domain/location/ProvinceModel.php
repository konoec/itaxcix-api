<?php

namespace itaxcix\Core\Domain\location;

use itaxcix\Infrastructure\Database\Entity\location\ProvinceEntity;
use JsonSerializable;

class ProvinceModel implements JsonSerializable {
    private ?int $id;
    private ?string $name = null;
    private ?DepartmentModel $department = null;
    private ?string $ubigeo = null;

    /**
     * @param ?int $id
     * @param string|null $name
     * @param DepartmentModel|null $department
     * @param string|null $ubigeo
     */
    public function __construct(?int $id, ?string $name, ?DepartmentModel $department, ?string $ubigeo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->department = $department;
        $this->ubigeo = $ubigeo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
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

    public function getDepartment(): ?DepartmentModel
    {
        return $this->department;
    }

    public function setDepartment(?DepartmentModel $department): void
    {
        $this->department = $department;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): void
    {
        $this->ubigeo = $ubigeo;
    }

    public function toEntity(): ProvinceEntity
    {
        $provinceEntity = new ProvinceEntity();
        if ($this->id !== null) {
            $provinceEntity->setId($this->id);
        }
        $provinceEntity->setName($this->name);
        $provinceEntity->setDepartment($this->department?->toEntity());
        $provinceEntity->setUbigeo($this->ubigeo);

        return $provinceEntity;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'department' => $this->department?->jsonSerialize(),
            'ubigeo' => $this->ubigeo
        ];
    }
}