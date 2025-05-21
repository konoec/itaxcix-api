<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol_permiso')]
class RolePermissionEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolp_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: RoleEntity::class)]
    #[ORM\JoinColumn(name: 'rolp_rol_id', referencedColumnName: 'rol_id')]
    private ?RoleEntity $role = null;
    #[ORM\ManyToOne(targetEntity: PermissionEntity::class)]
    #[ORM\JoinColumn(name: 'rolp_permiso_id', referencedColumnName: 'perm_id')]
    private ?PermissionEntity $permission = null;
    #[ORM\Column(name: 'rolp_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}