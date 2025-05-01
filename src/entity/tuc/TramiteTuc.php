<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_tramite_tuc")]
class TramiteTuc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "tram_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "tram_codigo", type: "string", length: 8, nullable: true)]
    private ?string $codigo = null;

    #[ORM\Column(name: "tram_tipo", type: "string", length: 50, nullable: true)]
    private ?string $tipo = null;

    #[ORM\Column(name: "tram_fecha_tramite", type: "date", nullable: true)]
    private ?\DateTime $fechaTramite = null;

    #[ORM\Column(name: "tram_fecha_emision", type: "date", nullable: true)]
    private ?\DateTime $fechaEmision = null;

    #[ORM\Column(name: "tram_fecha_caducidad", type: "date", nullable: true)]
    private ?\DateTime $fechaCaducidad = null;

    #[ORM\Column(name: "tram_modalidad", type: "string", length: 50, nullable: true)]
    private ?string $modalidad = null;

    #[ORM\ManyToOne(targetEntity: Conductor::class)]
    #[ORM\JoinColumn(name: "tran_conductor_id", referencedColumnName: "cond_id")]
    private Conductor $conductor;

    #[ORM\ManyToOne(targetEntity: Vehiculo::class)]
    #[ORM\JoinColumn(name: "tram_vehiculo_id", referencedColumnName: "vehi_id")]
    private Vehiculo $vehiculo;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: "tram_empresa_id", referencedColumnName: "empr_id")]
    private Empresa $empresa;

    #[ORM\ManyToOne(targetEntity: UbicacionGeografica::class)]
    #[ORM\JoinColumn(name: "tram_ubic_id", referencedColumnName: "ubic_id")]
    private UbicacionGeografica $ubicacion;

    #[ORM\ManyToOne(targetEntity: EstadoTuc::class)]
    #[ORM\JoinColumn(name: "tram_estado_id", referencedColumnName: "esta_id")]
    private EstadoTuc $estado;

    // Getters y setters
}