<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_tipo_combustible")]
class TipoCombustible
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "tipo_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "tipo_nombre", type: "string", length: 50)]
    private string $nombre;

    #[ORM\Column(name: "tipo_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}