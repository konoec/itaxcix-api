<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol_permiso')]
class RolPermiso {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolp_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Rol::class)]
    #[ORM\JoinColumn(name: 'rolp_rol_id', referencedColumnName: 'rol_id')]
    private ?Rol $rol = null;

    #[ORM\ManyToOne(targetEntity: Permiso::class)]
    #[ORM\JoinColumn(name: 'rolp_permiso_id', referencedColumnName: 'perm_id')]
    private ?Permiso $permiso = null;

    #[ORM\Column(name: 'rolp_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}