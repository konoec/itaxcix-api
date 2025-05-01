<?php


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tb_vehiculo")]
class Vehiculo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "vehi_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "vehi_placa", type: "string", length: 10, unique: true)]
    private string $placa;

    #[ORM\Column(name: "vehi_modelo", type: "string", length: 50, nullable: true)]
    private ?string $modelo = null;

    #[ORM\Column(name: "vehi_color", type: "string", length: 30, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(name: "vehi_anio_fabric", type: "integer", nullable: true)]
    private ?int $anioFabricacion = null;

    #[ORM\Column(name: "vehi_num_asientos", type: "integer", nullable: true)]
    private ?int $numAsientos = null;

    #[ORM\Column(name: "vehi_num_pasajeros", type: "integer", nullable: true)]
    private ?int $numPasajeros = null;

    #[ORM\Column(name: "vehi_activo", type: "boolean")]
    private bool $activo = true;

    #[ORM\OneToOne(targetEntity: \EspecificacionTecnica::class, mappedBy: "vehiculo")]
    private ?\EspecificacionTecnica $especificacion = null;

    #[ORM\ManyToOne(targetEntity: Marca::class)]
    #[ORM\JoinColumn(name: "vehi_marca_id", referencedColumnName: "marc_id")]
    private Marca $marca;

    #[ORM\ManyToOne(targetEntity: TipoCombustible::class)]
    #[ORM\JoinColumn(name: "vehi_tipo_combustible_id", referencedColumnName: "tipo_id")]
    private TipoCombustible $tipoCombustible;

    #[ORM\ManyToOne(targetEntity: ClaseVehiculo::class)]
    #[ORM\JoinColumn(name: "vehi_clase_id", referencedColumnName: "clas_id")]
    private ClaseVehiculo $clase;

    #[ORM\ManyToOne(targetEntity: CategoriaVehiculo::class)]
    #[ORM\JoinColumn(name: "vehi_categoria_id", referencedColumnName: "cate_id")]
    private CategoriaVehiculo $categoria;

    // Getters y setters
}