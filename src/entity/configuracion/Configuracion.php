<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_configuracion")]
class Configuracion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "conf_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "conf_clave", type: "string", length: 50)]
    private string $clave;

    #[ORM\Column(name: "conf_valor", type: "string", length: 255, nullable: true)]
    private ?string $valor = null;

    #[ORM\Column(name: "conf_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}