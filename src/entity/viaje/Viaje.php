<?php

namespace itaxcix\entity\viaje;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\entity\ubicacion\Coordenadas;
use itaxcix\entity\usuario\Ciudadano;
use itaxcix\entity\usuario\Conductor;

#[ORM\Entity]
#[ORM\Table(name: 'tb_viaje')]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'viaj_id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ciudadano::class)]
    #[ORM\JoinColumn(name: 'viaj_ciudadano_id', referencedColumnName: 'ciud_id')]
    private ?Ciudadano $ciudadano = null;

    #[ORM\ManyToOne(targetEntity: Conductor::class)]
    #[ORM\JoinColumn(name: 'viaj_conductor_id', referencedColumnName: 'cond_id')]
    private ?Conductor $conductor = null;

    #[ORM\ManyToOne(targetEntity: Coordenadas::class)]
    #[ORM\JoinColumn(name: 'viaj_origen_id', referencedColumnName: 'coor_id')]
    private ?Coordenadas $origen = null;

    #[ORM\ManyToOne(targetEntity: Coordenadas::class)]
    #[ORM\JoinColumn(name: 'viaj_destino_id', referencedColumnName: 'coor_id')]
    private ?Coordenadas $destino = null;

    #[ORM\Column(type: 'datetime', name: 'viaj_fecha_inicio', nullable: true)]
    private ?\DateTime $fechaInicio = null;

    #[ORM\Column(type: 'datetime', name: 'viaj_fecha_fin', nullable: true)]
    private ?\DateTime $fechaFin = null;

    #[ORM\Column(type: 'datetime', name: 'viaj_fecha_creacion')]
    private \DateTime $fechaCreacion;

    #[ORM\ManyToOne(targetEntity: EstadoViaje::class)]
    #[ORM\JoinColumn(name: 'viaj_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoViaje $estado = null;
}