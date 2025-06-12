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

    public function __construct()
    {
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUser(): ?UserEntity { return $this->user; }
    public function setUser(?UserEntity $user): void { $this->user = $user; }

    public function getTravel(): ?TravelEntity { return $this->travel; }
    public function setTravel(?TravelEntity $travel): void { $this->travel = $travel; }

    public function getType(): ?IncidentTypeEntity { return $this->type; }
    public function setType(?IncidentTypeEntity $type): void { $this->type = $type; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): void { $this->comment = $comment; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): void { $this->active = $active; }
}