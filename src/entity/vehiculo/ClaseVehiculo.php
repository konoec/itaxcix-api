<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_clase_vehiculo")]
class ClaseVehiculo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "clas_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "clas_nombre", type: "string", length: 50)]
    private string $nombre;

    #[ORM\Column(name: "clas_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}