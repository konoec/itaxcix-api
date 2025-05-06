<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\usuario\PerfilConductorRepository;

#[ORM\Entity(repositoryClass: PerfilConductorRepository::class)]
#[ORM\Table(name: 'tb_perfil_conductor')]
class PerfilConductor {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perf_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'perf_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: 'perf_disponible', type: 'boolean', options: ['default' => false])]
    private bool $disponible = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): void
    {
        $this->disponible = $disponible;
    }
}