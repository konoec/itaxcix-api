<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_ubicacion")]
class Ubicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ubic_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "ubic_nombre", type: "string", length: 100)]
    private string $nombre;

    #[ORM\Column(name: "ubic_latitud", type: "string", length: 20)]
    private string $latitud;

    #[ORM\Column(name: "ubic_longitud", type: "string", length: 20)]
    private string $longitud;

    // Getters y setters
}