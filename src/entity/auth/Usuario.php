<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_usuario")]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "usua_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "usua_alias", type: "string", length: 50, nullable: true)]
    private ?string $alias = null;

    #[ORM\Column(name: "usua_clave", type: "string", length: 255)]
    private string $clave;

    #[ORM\Column(name: "usua_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: \Persona::class, inversedBy: "usuario")]
    #[ORM\JoinColumn(name: "usua_persona_id", referencedColumnName: "pers_id", nullable: false)]
    private \Persona $persona;

    // Getters y setters
}