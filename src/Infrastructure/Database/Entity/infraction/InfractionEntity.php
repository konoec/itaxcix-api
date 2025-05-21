<?php

namespace itaxcix\Infrastructure\Database\Entity\infraction;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_infraccion')]
class InfractionEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'infr_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'infr_usuario_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $user = null;
    #[ORM\ManyToOne(targetEntity: InfractionSeverityEntity::class)]
    #[ORM\JoinColumn(name: 'infr_gravedad_id', referencedColumnName: 'grav_id')]
    private ?InfractionSeverityEntity $severity = null;
    #[ORM\Column(name: 'infr_fecha', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $date;
    #[ORM\Column(name: 'infr_descripcion', type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\ManyToOne(targetEntity: InfractionStatusEntity::class)]
    #[ORM\JoinColumn(name: 'infr_estado_id', referencedColumnName: 'esta_id')]
    private ?InfractionStatusEntity $status = null;
}