<?php

namespace itaxcix\Shared\DTO\useCases\Province;

class ProvinceRequestDTO
{
    private ?string $name;
    private ?int $departmentId;
    private ?string $ubigeo;

    public function __construct(?string $name = null, ?int $departmentId = null, ?string $ubigeo = null)
    {
        $this->name = $name;
        $this->departmentId = $departmentId;
        $this->ubigeo = $ubigeo;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function setDepartmentId(?int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): void
    {
        $this->ubigeo = $ubigeo;
    }
}
