<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol_usuario')]
class RolUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolu_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Rol::class)]
    #[ORM\JoinColumn(name: 'rolu_rol_id', referencedColumnName: 'rol_id')]
    private ?Rol $rol = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'rolu_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: 'rolu_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}