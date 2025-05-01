<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_conductor")]
class Conductor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "cond_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "cond_disponible", type: "boolean")]
    private bool $disponible = false;

    #[ORM\Column(name: "cond_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "cond_usuario_id", referencedColumnName: "usua_id")]
    private Usuario $usuario;

    // Getters y setters
}