<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineProvinceRepository;

#[ORM\Entity(repositoryClass: DoctrineProvinceRepository::class)]
#[ORM\Table(name: 'tb_provincia')]
class ProvinceEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'prov_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'prov_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $name = null;
    #[ORM\ManyToOne(targetEntity: DepartmentEntity::class)]
    #[ORM\JoinColumn(name: 'prov_departamento_id', referencedColumnName: 'depa_id')]
    private ?DepartmentEntity $department = null;
    #[ORM\Column(name: 'prov_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
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

    public function getDepartment(): ?DepartmentEntity
    {
        return $this->department;
    }

    public function setDepartment(?DepartmentEntity $department): void
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


}