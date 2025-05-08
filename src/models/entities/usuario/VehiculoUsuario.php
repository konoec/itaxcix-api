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

    #[ORM\ManyToOne(targetEntity: Vehiculo::class)]
    #[ORM\JoinColumn(name: 'vehi_vehiculo_id', referencedColumnName: 'vehi_id', nullable: false)]
    private ?Vehiculo $vehiculo = null;

    #[ORM\Column(name: 'cont_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $activo = true;
}