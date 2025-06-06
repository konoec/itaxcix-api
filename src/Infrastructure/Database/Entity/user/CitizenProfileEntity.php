<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_perfil_ciudadano')]
class CitizenProfileEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perf_id', type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'perf_usuario_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $user = null;

    #[ORM\Column(name: 'perf_calificacion_promedio', type: 'decimal', precision: 3, scale: 2, nullable: false, options: ['default' => 0.00])]
    private float $averageRating = 0.00;

    #[ORM\Column(name: 'perf_numero_calificaciones', type: 'integer', nullable: false, options: ['default' => 0])]
    private int $ratingCount = 0;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function setAverageRating(float $averageRating): void
    {
        $this->averageRating = $averageRating;
    }

    public function getRatingCount(): int
    {
        return $this->ratingCount;
    }

    public function setRatingCount(int $ratingCount): void
    {
        $this->ratingCount = $ratingCount;
    }
}