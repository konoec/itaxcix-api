<?php

namespace itaxcix\model\entities\usuario;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\model\entities\persona\Persona;

#[ORM\Entity]
#[ORM\Table(name: 'tb_usuario')]
class Usuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'usua_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'usua_alias', type: 'string', length: 50, nullable: true)]
    private ?string $alias = null;

    #[ORM\Column(name: 'usua_clave', type: 'string', length: 255)]
    private string $clave;

    #[ORM\ManyToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(name: 'usua_persona_id', referencedColumnName: 'pers_id')]
    private ?Persona $persona = null;

    #[ORM\ManyToOne(targetEntity: EstadoUsuario::class)]
    #[ORM\JoinColumn(name: 'usua_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoUsuario $estado = null;
}