<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_incidencia")]
class Incidencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "inci_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "inci_descripcion", type: "text", nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(name: "inci_fecha", type: "datetime")]
    private \DateTime $fecha;

    #[ORM\Column(name: "inci_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "inci_usuario_id", referencedColumnName: "usua_id")]
    private Usuario $usuario;

    #[ORM\ManyToOne(targetEntity: Viaje::class)]
    #[ORM\JoinColumn(name: "inci_viaje_id", referencedColumnName: "viaj_id")]
    private Viaje $viaje;

    #[ORM\ManyToOne(targetEntity: TipoIncidencia::class)]
    #[ORM\JoinColumn(name: "inci_tipo_id", referencedColumnName: "tipo_id")]
    private TipoIncidencia $tipo;

    // Getters y setters
}