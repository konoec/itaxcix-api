<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_categoria_vehiculo")]
class CategoriaVehiculo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "cate_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "cate_nombre", type: "string", length: 50)]
    private string $nombre;

    #[ORM\Column(name: "cate_activo", type: "boolean")]
    private bool $activo = true;

    // Getters y setters
}