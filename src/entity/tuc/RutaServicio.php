<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_ruta_servicio")]
class RutaServicio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ruta_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "ruta_tipo_servicio", type: "string", length: 50, nullable: true)]
    private ?string $tipoServicio = null;

    #[ORM\Column(name: "ruta_texto", type: "text")]
    private string $texto;

    #[ORM\Column(name: "ruta_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: TramiteTuc::class)]
    #[ORM\JoinColumn(name: "ruta_tram_id", referencedColumnName: "tram_id")]
    private TramiteTuc $tramite;

    // Getters y setters
}