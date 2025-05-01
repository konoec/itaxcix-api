<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_ciudadano")]
class Ciudadano
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ciud_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "esta_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "ciud_usuario_id", referencedColumnName: "usua_id")]
    private Usuario $usuario;

    // Getters y setters
}