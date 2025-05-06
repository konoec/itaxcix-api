<?php

namespace itaxcix\models\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\ubicacion\DepartamentoRepository;

#[ORM\Entity(repositoryClass: DepartamentoRepository::class)]
#[ORM\Table(name: 'tb_departamento')]
class Departamento {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'depa_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'depa_nombre', type: 'string', length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'depa_ubigeo', type: 'string', length: 6, nullable: true)]
    private ?string $ubigeo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
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