<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\entities\vehiculo\Vehiculo;
use itaxcix\repositories\usuario\VehiculoUsuarioRepository;

#[ORM\Entity(repositoryClass: VehiculoUsuarioRepository::class)]
#[ORM\Table(name: 'tb_vehiculo_usuario')]
class VehiculoUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cont_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'cont_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: Vehiculo::class, inversedBy: 'vehiculoUsuarios')]
    #[ORM\JoinColumn(name: 'vehi_vehiculo_id', referencedColumnName: 'vehi_id', nullable: false)]
    private ?Vehiculo $vehiculo = null;

    #[ORM\Column(name: 'cont_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $activo = true;

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

    public function getVehiculo(): ?Vehiculo
    {
        return $this->vehiculo;
    }

    public function setVehiculo(?Vehiculo $vehiculo): void
    {
        $this->vehiculo = $vehiculo;
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