<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_rol')]
class Rol {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rol_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'rol_nombre', type: 'string', length: 50, unique: true)]
    private string $nombre;

    #[ORM\Column(name: 'rol_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}