<?php

namespace itaxcix\model\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_permiso')]
class Permiso {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perm_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'perm_nombre', type: 'string', length: 100, unique: true)]
    private string $nombre;

    #[ORM\Column(name: 'perm_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}