<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_coordenadas')]
class CoordinatesEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'coor_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'coor_nombre', type: 'string', length: 100)]
    private string $name;
    #[ORM\ManyToOne(targetEntity: DistrictEntity::class)]
    #[ORM\JoinColumn(name: 'coor_distrito_id', referencedColumnName: 'dist_id')]
    private ?DistrictEntity $district = null;
    #[ORM\Column(name: 'coor_latitud', type: 'string', length: 20)]
    private string $latitude;
    #[ORM\Column(name: 'coor_longitud', type: 'string', length: 20)]
    private string $longitude;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getDistrict(): ?DistrictEntity
    {
        return $this->district;
    }

    public function setDistrict(?DistrictEntity $district): void
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