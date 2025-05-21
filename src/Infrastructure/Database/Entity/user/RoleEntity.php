<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol')]
class RoleEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rol_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'rol_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'rol_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}