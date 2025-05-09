<?php

namespace itaxcix\models\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\ubicacion\DistritoRepository;

#[ORM\Entity(repositoryClass: DistritoRepository::class)]
#[ORM\Table(name: 'tb_distrito')]
class Distrito {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'dist_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'dist_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(targetEntity: Provincia::class)]
    #[ORM\JoinColumn(name: 'dist_provincia_id', referencedColumnName: 'prov_id')]
    private ?Provincia $provincia = null;

    #[ORM\Column(name: 'dist_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): void
    {
        $this->provincia = $provincia;
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