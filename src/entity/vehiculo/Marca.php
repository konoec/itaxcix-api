<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_marca")]
class Marca
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "marc_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "marc_nombre", type: "string", length: 50)]
    private string $nombre;

    #[ORM\Column(name: "marc_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}