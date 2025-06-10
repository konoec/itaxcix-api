<?php

namespace itaxcix\Core\Domain\location;

class CoordinatesModel {
    private ?int $id = null;
    private string $name;
    private ?DistrictModel $district = null;
    private string $latitude;
    private string $longitude;

    /**
     * @param int|null $id
     * @param string $name
     * @param DistrictModel|null $district
     * @param string $latitude
     * @param string $longitude
     */
    public function __construct(?int $id, string $name, ?DistrictModel $district, string $latitude, string $longitude)
    {
        $this->id = $id;
        $this->name = $name;
        $this->district = $district;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDistrict(): ?DistrictModel
    {
        return $this->district;
    }

    public function setDistrict(?DistrictModel $district): void
    {
        $this->district = $district;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
    }

}