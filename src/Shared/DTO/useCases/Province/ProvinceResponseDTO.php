<?php

namespace itaxcix\Shared\DTO\useCases\Province;

use itaxcix\Core\Domain\location\ProvinceModel;

class ProvinceResponseDTO
{
    private ?int $id;
    private ?string $name;
    private ?int $departmentId;
    private ?string $departmentName;
    private ?string $ubigeo;

    public function __construct(?int $id = null, ?string $name = null, ?int $departmentId = null, ?string $departmentName = null, ?string $ubigeo = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->departmentId = $departmentId;
        $this->departmentName = $departmentName;
        $this->ubigeo = $ubigeo;
    }

    public static function fromModel(ProvinceModel $province): self
    {
        return new self(
            $province->getId(),
            $province->getName(),
            $province->getDepartment()?->getId(),
            $province->getDepartment()?->getName(),
            $province->getUbigeo()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'departmentId' => $this->departmentId,
            'departmentName' => $this->departmentName,
            'ubigeo' => $this->ubigeo
        ];
    }
}
