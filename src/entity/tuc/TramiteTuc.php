<?php

namespace itaxcix\entity\tuc;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\entity\ubicacion\Distrito;
use itaxcix\entity\usuario\Conductor;
use itaxcix\entity\vehiculo\Vehiculo;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tramite_tuc')]
class TramiteTuc {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tram_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tram_codigo', type: 'string', length: 8, nullable: true)]
    private ?string $codigo = null;

    #[ORM\ManyToOne(targetEntity: Conductor::class)]
    #[ORM\JoinColumn(name: 'tran_conductor_id', referencedColumnName: 'cond_id')]
    private ?Conductor $conductor = null;

    #[ORM\ManyToOne(targetEntity: Vehiculo::class)]
    #[ORM\JoinColumn(name: 'tram_vehiculo_id', referencedColumnName: 'vehi_id')]
    private ?Vehiculo $vehiculo = null;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'tram_empresa_id', referencedColumnName: 'empr_id')]
    private ?Empresa $empresa = null;

    #[ORM\ManyToOne(targetEntity: Distrito::class)]
    #[ORM\JoinColumn(name: 'tram_distrito_id', referencedColumnName: 'dist_id')]
    private ?Distrito $distrito = null;

    #[ORM\ManyToOne(targetEntity: EstadoTuc::class)]
    #[ORM\JoinColumn(name: 'tram_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoTuc $estado = null;

    #[ORM\ManyToOne(targetEntity: TipoTramite::class)]
    #[ORM\JoinColumn(name: 'tram_tipo_id', referencedColumnName: 'tipo_id')]
    private ?TipoTramite $tipo = null;

    #[ORM\Column(name: 'tram_fecha_tramite', type: 'date', nullable: true)]
    private ?\DateTime $fechaTramite = null;

    #[ORM\Column(name: 'tram_fecha_emision', type: 'date', nullable: true)]
    private ?\DateTime $fechaEmision = null;

    #[ORM\Column(name: 'tram_fecha_caducidad', type: 'date', nullable: true)]
    private ?\DateTime $fechaCaducidad = null;

    #[ORM\Column(name: 'tram_modalidad', type: 'string', length: 50, nullable: true)]
    private ?string $modalidad = null;
}