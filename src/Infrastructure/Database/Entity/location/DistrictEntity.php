<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineDistrictRepository;

#[ORM\Entity(repositoryClass: DoctrineDistrictRepository::class)]
#[ORM\Table(name: 'tb_distrito')]
class DistrictEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'dist_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'dist_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $name = null;
    #[ORM\ManyToOne(targetEntity: ProvinceEntity::class)]
    #[ORM\JoinColumn(name: 'dist_provincia_id', referencedColumnName: 'prov_id')]
    private ?ProvinceEntity $province = null;
    #[ORM\Column(name: 'dist_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
    private ?string $ubigeo = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getProvince(): ?ProvinceEntity
    {
        return $this->province;
    }

    public function setProvince(?ProvinceEntity $province): void
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


}