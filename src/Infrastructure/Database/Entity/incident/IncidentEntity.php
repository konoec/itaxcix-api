<?php

namespace itaxcix\Infrastructure\Database\Entity\incident;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_incidencia')]
class IncidentEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'inci_id', type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'inci_usuario_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $user = null;
    #[ORM\ManyToOne(targetEntity: TravelEntity::class)]
    #[ORM\JoinColumn(name: 'inci_viaje_id', referencedColumnName: 'viaj_id')]
    private ?TravelEntity $travel = null;
    #[ORM\ManyToOne(targetEntity: IncidentTypeEntity::class)]
    #[ORM\JoinColumn(name: 'inci_tipo_id', referencedColumnName: 'tipo_id')]
    private ?IncidentTypeEntity $type = null;
    #[ORM\Column(name: 'inci_comentario', type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;
    #[ORM\Column(name: 'inci_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}