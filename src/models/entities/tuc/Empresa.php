<?php

namespace itaxcix\models\entities\tuc;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\tuc\EmpresaRepository;

#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
#[ORM\Table(name: 'tb_empresa')]
class Empresa {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'empr_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'empr_ruc', type: 'string', length: 11, nullable: true)]
    private ?string $ruc = null;

    #[ORM\Column(name: 'empr_nombre', type: 'string', length: 100)]
    private string $nombre;

    #[ORM\Column(name: 'empr_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getRuc(): ?string
    {
        return $this->ruc;
    }

    public function setRuc(?string $ruc): void
    {
        $this->ruc = $ruc;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }
}