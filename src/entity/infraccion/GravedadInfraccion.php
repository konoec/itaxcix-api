<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_gravedad_infraccion")]
class GravedadInfraccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "grav_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "grav_nombre", type: "string", length: 100, unique: true)]
    private string $nombre;

    #[ORM\Column(name: "grav_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}