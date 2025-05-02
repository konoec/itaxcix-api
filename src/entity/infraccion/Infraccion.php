<?php

namespace itaxcix\entity\infraccion;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\entity\usuario\Conductor;

#[ORM\Entity]
#[ORM\Table(name: 'tb_infraccion')]
class Infraccion {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'infr_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Conductor::class)]
    #[ORM\JoinColumn(name: 'infr_conductor_id', referencedColumnName: 'cond_id')]
    private ?Conductor $conductor = null;

    #[ORM\ManyToOne(targetEntity: GravedadInfraccion::class)]
    #[ORM\JoinColumn(name: 'infr_gravedad_id', referencedColumnName: 'grav_id')]
    private ?GravedadInfraccion $gravedad = null;

    #[ORM\Column(name: 'infr_fecha', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha;

    #[ORM\Column(name: 'infr_descripcion', type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne(targetEntity: EstadoInfraccion::class)]
    #[ORM\JoinColumn(name: 'infr_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoInfraccion $estado = null;
}