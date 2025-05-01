<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_viaje")]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "viaj_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "viaj_fecha_inicio", type: "datetime", nullable: true)]
    private ?\DateTime $fechaInicio = null;

    #[ORM\Column(name: "viaj_fecha_fin", type: "datetime", nullable: true)]
    private ?\DateTime $fechaFin = null;

    #[ORM\Column(name: "viaj_fecha_creacion", type: "datetime")]
    private \DateTime $fechaCreacion;

    #[ORM\ManyToOne(targetEntity: Ciudadano::class)]
    #[ORM\JoinColumn(name: "viaj_ciudadano_id", referencedColumnName: "ciud_id")]
    private Ciudadano $ciudadano;

    #[ORM\ManyToOne(targetEntity: Conductor::class)]
    #[ORM\JoinColumn(name: "viaj_conductor_id", referencedColumnName: "cond_id")]
    private Conductor $conductor;

    #[ORM\ManyToOne(targetEntity: \Ubicacion::class)]
    #[ORM\JoinColumn(name: "viaj_origen_id", referencedColumnName: "ubic_id")]
    private \Ubicacion $origen;

    #[ORM\ManyToOne(targetEntity: \Ubicacion::class)]
    #[ORM\JoinColumn(name: "viaj_destino_id", referencedColumnName: "ubic_id")]
    private \Ubicacion $destino;

    #[ORM\ManyToOne(targetEntity: \EstadoViaje::class)]
    #[ORM\JoinColumn(name: "viaj_estado_id", referencedColumnName: "esta_id")]
    private \EstadoViaje $estado;

    // Getters y setters
}