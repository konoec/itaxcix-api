<?php

namespace itaxcix\Infrastructure\Database\Entity\travel;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\location\CoordinatesEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_viaje')]
class TravelEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'viaj_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_usuario_ciudadano_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $citizen = null;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_usuario_conductor_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $driver = null;
    #[ORM\ManyToOne(targetEntity: CoordinatesEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_origen_id', referencedColumnName: 'coor_id')]
    private ?CoordinatesEntity $origin = null;
    #[ORM\ManyToOne(targetEntity: CoordinatesEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_destino_id', referencedColumnName: 'coor_id')]
    private ?CoordinatesEntity $destination = null;
    #[ORM\Column(name: 'viaj_fecha_inicio', type: 'datetime', nullable: true)]
    private ?DateTime $startDate = null;
    #[ORM\Column(name: 'viaj_fecha_fin', type: 'datetime', nullable: true)]
    private ?DateTime $endDate = null;
    #[ORM\Column(name: 'viaj_fecha_creacion', type: 'datetime')]
    private DateTime $creationDate;
    #[ORM\ManyToOne(targetEntity: TravelStatusEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_estado_id', referencedColumnName: 'esta_id')]
    private TravelStatusEntity $status;
}