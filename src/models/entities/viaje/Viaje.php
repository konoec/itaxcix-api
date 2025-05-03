<?php

namespace itaxcix\models\entities\viaje;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\entities\ubicacion\Coordenadas;
use itaxcix\models\entities\usuario\Usuario;

#[ORM\Entity]
#[ORM\Table(name: 'tb_viaje')]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'viaj_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'viaj_usuario_ciudadano_id', referencedColumnName: 'usua_id')]
    private ?Usuario $ciudadano = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'viaj_usuario_conductor_id', referencedColumnName: 'usua_id')]
    private ?Usuario $conductor = null;

    #[ORM\ManyToOne(targetEntity: Coordenadas::class)]
    #[ORM\JoinColumn(name: 'viaj_origen_id', referencedColumnName: 'coor_id')]
    private ?Coordenadas $origen = null;

    #[ORM\ManyToOne(targetEntity: Coordenadas::class)]
    #[ORM\JoinColumn(name: 'viaj_destino_id', referencedColumnName: 'coor_id')]
    private ?Coordenadas $destino = null;

    #[ORM\Column(name: 'viaj_fecha_inicio', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaInicio = null;

    #[ORM\Column(name: 'viaj_fecha_fin', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaFin = null;

    #[ORM\Column(name: 'viaj_fecha_creacion', type: 'datetime')]
    private \DateTime $fechaCreacion;

    #[ORM\ManyToOne(targetEntity: EstadoViaje::class)]
    #[ORM\JoinColumn(name: 'viaj_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoViaje $estado = null;
}