<?php

namespace itaxcix\Infrastructure\Database\Entity\rating;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_calificacion')]
class RatingEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cali_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'cali_calificador_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $rater = null;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'cali_calificado_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $rated = null;
    #[ORM\ManyToOne(targetEntity: TravelEntity::class)]
    #[ORM\JoinColumn(name: 'cali_viaje_id', referencedColumnName: 'viaj_id')]
    private ?TravelEntity $travel = null;
    #[ORM\Column(name: 'cali_puntaje', type: 'integer', nullable: false)]
    private int $score;
    #[ORM\Column(name: 'cali_comentario', type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;
}