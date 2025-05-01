<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_administrador")]
class Administrador
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "admi_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "admi_area", type: "string", length: 100)]
    private string $area;

    #[ORM\Column(name: "admi_cargo", type: "string", length: 100)]
    private string $cargo;

    #[ORM\Column(name: "admi_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "admi_usuario_id", referencedColumnName: "usua_id")]
    private Usuario $usuario;

    // Getters y setters
}