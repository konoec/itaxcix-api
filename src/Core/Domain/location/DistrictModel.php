<?php

namespace itaxcix\Core\Domain\location;

use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;

class DistrictModel {
    private int $id;
    private ?string $name = null;
    private ?ProvinceModel $province = null;
    private ?string $ubigeo = null;

    /**
     * @param int $id
     * @param string|null $name
     * @param ProvinceModel|null $province
     * @param string|null $ubigeo
     */
    public function __construct(int $id, ?string $name, ?ProvinceModel $province, ?string $ubigeo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->province = $province;
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

    public function getProvince(): ?ProvinceModel
    {
        return $this->province;
    }

    public function setProvince(?ProvinceModel $province): void
    {
        $this->province = $province;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): void
    {
        $this->ubigeo = $ubigeo;
    }

    public function toEntity(): DistrictEntity
    {
        $districtEntity = new DistrictEntity();
        $districtEntity->setId($this->id);
        $districtEntity->setName($this->name);
        $districtEntity->setUbigeo($this->ubigeo);
        $districtEntity->setProvince($this->province?->toEntity());

        return $districtEntity;
    }
}