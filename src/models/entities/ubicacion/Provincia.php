<?php

namespace itaxcix\models\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\ubicacion\ProvinciaRepository;

#[ORM\Entity(repositoryClass: ProvinciaRepository::class)]
#[ORM\Table(name: 'tb_provincia')]
class Provincia {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'prov_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'prov_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(targetEntity: Departamento::class)]
    #[ORM\JoinColumn(name: 'prov_departamento_id', referencedColumnName: 'depa_id')]
    private ?Departamento $departamento = null;

    #[ORM\Column(name: 'prov_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
    private ?string $ubigeo = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNombre(): ?string {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getDepartamento(): ?Departamento {
        return $this->departamento;
    }

    public function setDepartamento(?Departamento $departamento): void {
        $this->departamento = $departamento;
    }

    public function getUbigeo(): ?string {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): void {
        $this->ubigeo = $ubigeo;
    }
}