<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol_usuario')]
class UserRoleEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolu_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: RoleEntity::class)]
    #[ORM\JoinColumn(name: 'rolu_rol_id', referencedColumnName: 'rol_id', nullable: false)]
    private ?RoleEntity $role = null;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'rolu_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private ?UserEntity $user = null;
    #[ORM\Column(name: 'rolu_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}