<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_permiso')]
class PermissionEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perm_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'perm_nombre', type: 'string', length: 100, unique: true)]
    private string $name;
    #[ORM\Column(name: 'perm_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}