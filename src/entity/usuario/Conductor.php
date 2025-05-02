<?php

namespace itaxcix\entity\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_conductor')]
class Conductor {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cond_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'cond_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: 'cond_disponible', type: 'boolean', options: ['default' => false])]
    private bool $disponible = false;

    #[ORM\Column(name: 'cond_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}