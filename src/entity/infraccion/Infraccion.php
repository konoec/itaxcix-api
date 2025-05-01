<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_infraccion")]
class Infraccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "infr_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "infr_descripcion", type: "text", nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(name: "infr_fecha", type: "datetime")]
    private \DateTime $fecha;

    #[ORM\ManyToOne(targetEntity: Conductor::class)]
    #[ORM\JoinColumn(name: "infr_conductor_id", referencedColumnName: "cond_id")]
    private Conductor $conductor;

    #[ORM\ManyToOne(targetEntity: GravedadInfraccion::class)]
    #[ORM\JoinColumn(name: "infr_gravedad_id", referencedColumnName: "grav_id")]
    private GravedadInfraccion $gravedad;

    #[ORM\ManyToOne(targetEntity: EstadoInfraccion::class)]
    #[ORM\JoinColumn(name: "infr_estado_id", referencedColumnName: "esta_id")]
    private EstadoInfraccion $estado;

    // Getters y setters
}