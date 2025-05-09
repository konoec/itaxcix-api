<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\repositories\vehiculo\VehiculoRepository;

#[ORM\Entity(repositoryClass: VehiculoRepository::class)]
#[ORM\Table(name: 'tb_vehiculo')]
class Vehiculo {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'vehi_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'vehi_placa', type: 'string', length: 10, unique: true)]
    private string $placa;

    #[ORM\ManyToOne(targetEntity: Modelo::class)]
    #[ORM\JoinColumn(name: 'vehi_modelo_id', referencedColumnName: 'mode_id')]
    private ?Modelo $modelo = null;

    #[ORM\ManyToOne(targetEntity: Color::class)]
    #[ORM\JoinColumn(name: 'vehi_color_id', referencedColumnName: 'colo_id')]
    private ?Color $color = null;

    #[ORM\Column(name: 'vehi_anio_fabric', type: 'integer', nullable: true)]
    private ?int $anioFabricacion = null;

    #[ORM\Column(name: 'vehi_num_asientos', type: 'integer', nullable: true)]
    private ?int $numeroAsientos = null;

    #[ORM\Column(name: 'vehi_num_pasajeros', type: 'integer', nullable: true)]
    private ?int $numeroPasajeros = null;

    #[ORM\ManyToOne(targetEntity: TipoCombustible::class)]
    #[ORM\JoinColumn(name: 'vehi_tipo_combustible_id', referencedColumnName: 'tipo_id')]
    private ?TipoCombustible $tipoCombustible = null;

    #[ORM\ManyToOne(targetEntity: ClaseVehiculo::class)]
    #[ORM\JoinColumn(name: 'vehi_clase_id', referencedColumnName: 'clas_id')]
    private ?ClaseVehiculo $clase = null;

    #[ORM\ManyToOne(targetEntity: CategoriaVehiculo::class)]
    #[ORM\JoinColumn(name: 'vehi_categoria_id', referencedColumnName: 'cate_id')]
    private ?CategoriaVehiculo $categoria = null;

    #[ORM\Column(name: 'vehi_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $activo = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPlaca(): string
    {
        return $this->placa;
    }

    public function setPlaca(string $placa): void
    {
        $this->placa = $placa;
    }

    public function getModelo(): ?Modelo
    {
        return $this->modelo;
    }

    public function setModelo(?Modelo $modelo): void
    {
        $this->modelo = $modelo;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): void
    {
        $this->color = $color;
    }

    public function getAnioFabricacion(): ?int
    {
        return $this->anioFabricacion;
    }

    public function setAnioFabricacion(?int $anioFabricacion): void
    {
        $this->anioFabricacion = $anioFabricacion;
    }

    public function getNumeroAsientos(): ?int
    {
        return $this->numeroAsientos;
    }

    public function setNumeroAsientos(?int $numeroAsientos): void
    {
        $this->numeroAsientos = $numeroAsientos;
    }

    public function getNumeroPasajeros(): ?int
    {
        return $this->numeroPasajeros;
    }

    public function setNumeroPasajeros(?int $numeroPasajeros): void
    {
        $this->numeroPasajeros = $numeroPasajeros;
    }

    public function getTipoCombustible(): ?TipoCombustible
    {
        return $this->tipoCombustible;
    }

    public function setTipoCombustible(?TipoCombustible $tipoCombustible): void
    {
        $this->tipoCombustible = $tipoCombustible;
    }

    public function getClase(): ?ClaseVehiculo
    {
        return $this->clase;
    }

    public function setClase(?ClaseVehiculo $clase): void
    {
        $this->clase = $clase;
    }

    public function getCategoria(): ?CategoriaVehiculo
    {
        return $this->categoria;
    }

    public function setCategoria(?CategoriaVehiculo $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }
}